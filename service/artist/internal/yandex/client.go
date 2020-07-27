package yandex

import (
	"net/http"
	"time"
)

func NewDefaultClient() *http.Client {
	return &http.Client{
		Timeout: 5 * time.Second,
	}
}
