package app

import (
	"context"
	"fmt"
	"github.com/sigurniv/metalhead/service/tag/cmd/api/server"
	"github.com/sigurniv/metalhead/service/tag/internal/platform/config"
	"github.com/sirupsen/logrus"
)

type Application struct {
	Server *server.Server
	Config config.Config
	Logger *logrus.Logger
}

func New(config config.Config, logger *logrus.Logger) (app *Application, err error) {
	srv, err := server.New(config, logger)
	if err != nil {
		return nil, err
	}

	return &Application{
		Server: srv,
		Logger: logger,
		Config: config,
	}, err
}

func (app *Application) Run() {
	mode := "production"
	if app.Config.GetString("app.mode") == "debug"{
		mode = "debug"
	}

	app.Logger.Info(fmt.Sprintf("App running in %s mode, addr: %s", mode, app.Server.Srv.Addr))

	go app.Server.Run()
}

func (app *Application) Shutdown(ctx context.Context) error {
	app.Logger.Info("Shutting down app")

	var err error
	err = app.Server.Shutdown(ctx)

	return err
}
