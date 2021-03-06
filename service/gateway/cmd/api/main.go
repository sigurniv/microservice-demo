package main

import (
	"context"
	_ "expvar" //Register debug/vars handler
	"flag"
	"fmt"
	"github.com/sigurniv/metalhead/service/gateway/cmd/api/internal/app"
	"github.com/sigurniv/metalhead/service/gateway/internal/middleware"
	"github.com/sigurniv/metalhead/service/gateway/internal/platform/config"
	"github.com/sirupsen/logrus"
	"github.com/spf13/viper"
	"log"
	"net/http"
	_ "net/http/pprof" //Register debug/pprof handlers
	"os"
	"os/signal"
	"path/filepath"
	"time"
)

func main() {
	var configPath = flag.String("config", "", "absolute path to the config file directory")
	flag.Parse()
	c, err := initConfig(*configPath)
	if err != nil {
		log.Fatal(err.Error())
	}

	logger := logrus.New()
	logger.Out = os.Stdout

	//Start debug server
	go func() {
		debugPort := c.GetString("debug.port")
		logger.Printf("main: Debug service listening on %s", debugPort)
		err := http.ListenAndServe(":"+debugPort, http.DefaultServeMux)
		logger.Printf("main: Debug service ended %v", err)
	}()

	application, err := app.New(c, logger)
	if err != nil {
		log.Fatal(err.Error())
	}

	application.Server.AddMiddleware(middleware.Metrics())

	application.Run()

	quit := make(chan os.Signal)
	signal.Notify(quit, os.Interrupt)
	<-quit

	ctx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()

	application.Shutdown(ctx)
}

func initConfig(path string) (config.Config, error) {
	if path == "" {
		path = getBinaryDir(path)
	}

	v := viper.New()
	v.SetConfigName("config")
	v.AddConfigPath(path)
	err := v.ReadInConfig()
	if err != nil {
		return nil, fmt.Errorf("Fatal error config file: %s \n", err)
	}

	return config.New(v), err
}

func getBinaryDir(path string) string {
	if path == "" {
		path = "."
	}

	//current working app directory
	dir, err := filepath.Abs(filepath.Dir(os.Args[0]))
	if err == nil {
		path = dir
	}

	return path
}
