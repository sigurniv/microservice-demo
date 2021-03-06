package server

import (
	"context"
	"errors"
	"github.com/gorilla/mux"
	"github.com/sigurniv/metalhead/service/tag/cmd/api/handler"
	"github.com/sigurniv/metalhead/service/tag/internal/lastfm"
	"github.com/sigurniv/metalhead/service/tag/internal/platform/bus"
	"github.com/sigurniv/metalhead/service/tag/internal/platform/config"
	"github.com/sirupsen/logrus"
	"net/http"
)

type Server struct {
	Srv     *http.Server
	logger  *logrus.Logger
	config  config.Config
	Handler *handler.HttpHandler
}

func New(config config.Config, logger *logrus.Logger) (*Server, error) {
	var err error

	port := config.GetString("server.port")
	if port == "" {
		return nil, errors.New("server.port is not specified")
	}

	httpHandler := handler.NewHttpHandler(config, logger)

	srv := &Server{
		Srv:     &http.Server{Addr: ":" + port},
		logger:  logger,
		config:  config,
		Handler: httpHandler,
	}

	lastFM := lastfm.New(lastfm.NewDefaultClient(), config)

	natsUri := config.GetString("nats.uri")
	serviceBus := bus.New(logger, natsUri)
	natsHandler := handler.NewNatsHandler(logger, serviceBus)
	_, err = serviceBus.Nc.Subscribe("tag.tags", natsHandler.Tags(lastFM))
	_, err = serviceBus.Nc.Subscribe("artists.top", natsHandler.TopArtists(lastFM))
	if err != nil {
		logger.Error(err)
	}

	router := mux.NewRouter()
	router.HandleFunc("/", httpHandler.Info())
	router.HandleFunc("/artist/top/", httpHandler.TopArtists(lastFM))
	router.HandleFunc("/artist/tags/", httpHandler.Tags(lastFM))
	http.Handle("/", router)

	return srv, err
}

func (s *Server) Run() {
	if err := s.Srv.ListenAndServe(); err != nil {
		s.logger.Error("Error starting webserver", "err", err.Error())
		return
	}
}

func (s *Server) Shutdown(ctx context.Context) error {
	return s.Srv.Shutdown(ctx)
}
