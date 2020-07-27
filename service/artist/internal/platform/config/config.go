package config

import (
	"github.com/spf13/viper"
	"os"
)

type Config interface {
	GetString(key string) string
}

type EnvViperConfig struct {
	config *viper.Viper
}

func New(config *viper.Viper) *EnvViperConfig {
	return &EnvViperConfig{config}
}

func (c *EnvViperConfig) GetString(key string) string {
	envValue := os.Getenv(key)
	if envValue != "" {
		return envValue
	}

	return c.config.GetString(key)
}
