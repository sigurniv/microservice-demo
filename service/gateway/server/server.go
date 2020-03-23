package server

import (
	"context"
	"errors"
	"github.com/gorilla/mux"
	"github.com/patrickmn/go-cache"
	"github.com/sigurniv/metalhead/service/gateway/artist"
	"github.com/sigurniv/metalhead/service/gateway/bus"
	"github.com/sigurniv/metalhead/service/gateway/config"
	"github.com/sirupsen/logrus"
	"net/http"
	"time"
)

type Server struct {
	Srv     *http.Server
	logger  *logrus.Logger
	config  config.Config
	Handler *Handler
}

func New(config config.Config, logger *logrus.Logger) (*Server, error) {
	var err error

	port := config.GetString("server.port")
	if port == "" {
		return nil, errors.New("server.port is not specified")
	}

	handler := NewHandler(config, logger)

	srv := &Server{
		Srv:     &http.Server{Addr: ":" + port},
		logger:  logger,
		config:  config,
		Handler: handler,
	}

	natsUri := config.GetString("nats.uri")
	serviceBus := bus.New(logger, natsUri)
	artistService := artist.NewService(serviceBus, logger)
	c := cache.New(60*time.Minute, 10*time.Minute)

	router := mux.NewRouter()
	router.HandleFunc("/", handler.info())
	router.HandleFunc("/api/v1/artist/tags/", handler.tags(serviceBus, c))
	router.HandleFunc("/api/v1/artists/top/", handler.topArtists(artistService, c))
	router.HandleFunc("/api/v1/artist/", handler.getArtist(artistService, c))
	router.HandleFunc("/api/v1/auth/token/", handler.getToken(serviceBus))
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
