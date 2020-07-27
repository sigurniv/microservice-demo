package server

import (
	"encoding/json"
	"errors"
	"github.com/patrickmn/go-cache"
	"github.com/sigurniv/metalhead/service/gateway/internal/artist"
	"github.com/sigurniv/metalhead/service/gateway/internal/platform/bus"
	"github.com/sigurniv/metalhead/service/gateway/internal/platform/config"
	"github.com/sirupsen/logrus"
	"net/http"
	"time"
)

type Handler struct {
	config config.Config
	logger *logrus.Logger
}

func NewHandler(config config.Config, logger *logrus.Logger) *Handler {
	return &Handler{
		config: config,
		logger: logger,
	}
}

var errBadRequest = errors.New("bad request")
var errInternalError = errors.New("internal error")

func (h *Handler) info() http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		r, err := json.Marshal(InfoResponse{
			Data:  InfoData{h.config.GetString("service.name")},
			Error: "",
		})

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *Handler) tags(bus bus.IBus, c *cache.Cache) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		var tags []byte
		cacheKey := "tags"

		cachedTags, found := c.Get(cacheKey)
		if found {
			tags = cachedTags.([]byte)
		} else {
			response, err := bus.Request("tag.tags", []byte(""), 5*time.Second)
			if err != nil {
				h.logger.Printf("Error making NATS request: %v", err)
				h.errResponse(writer, errInternalError, 500)
				return
			}

			tags = response.Data
			c.Set(cacheKey, tags, cache.DefaultExpiration)
		}

		h.response(writer, tags)
	}
}

var errTagRequired = errors.New("tag param required")

func (h *Handler) topArtists(service artist.IArtistService, c *cache.Cache) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		tags, ok := request.URL.Query()["tag"]
		if !ok || len(tags[0]) < 1 {
			h.errResponse(writer, errTagRequired, http.StatusBadRequest)
			return
		}

		tag := tags[0]
		cacheKey := "artists.top." + tag
		cachedArtists, found := c.Get(cacheKey)
		var artists []byte
		if found {
			artists = cachedArtists.([]byte)
		} else {
			response, err := service.GetTopArtists(tag)
			if err != nil {
				h.logger.Printf("Error making NATS request: %v", err)
				h.errResponse(writer, errInternalError, 500)
				return
			}
			artists = response
			c.Set(cacheKey, artists, cache.DefaultExpiration)
		}

		h.response(writer, artists)
	}
}

func (h *Handler) getArtist(service artist.IArtistService, c *cache.Cache) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		names, ok := request.URL.Query()["name"]
		if !ok || len(names[0]) < 1 {
			h.errResponse(writer, errTagRequired, http.StatusBadRequest)
			return
		}

		name := names[0]
		cacheKey := "artist." + name
		cachedArtist, found := c.Get(cacheKey)
		var artists []byte
		if found {
			artists = cachedArtist.([]byte)
		} else {
			response, err := service.GetArtist(name)
			if err != nil {
				h.logger.Printf("Error making NATS request: %v", err)
				h.errResponse(writer, errInternalError, 500)
				return
			}
			artists = response
			c.Set(cacheKey, artists, cache.DefaultExpiration)
		}

		h.response(writer, artists)
	}
}

func (h *Handler) searchArtists(bus *bus.Bus, c *cache.Cache) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		name, ok := request.URL.Query()["name"]
		if !ok || len(name[0]) < 1 {
			h.errResponse(writer, errBadRequest, http.StatusBadRequest)
			return
		}

		var tags []byte
		requestAt := time.Now()
		names := name[0]
		cacheKey := "artists.search" + names

		cachedTags, found := c.Get(cacheKey)
		if found {
			tags = cachedTags.([]byte)
		} else {
			response, err := bus.Nc.Request("artists.search", []byte(names), 5*time.Second)
			if err != nil {
				h.logger.Printf("Error making NATS request: %v", err)
				h.errResponse(writer, errInternalError, 500)
				return
			}

			tags = response.Data
			c.Set(cacheKey, tags, cache.DefaultExpiration)
		}

		duration := time.Since(requestAt)
		h.logger.Printf("tag.tags scheduled in %+v\nResponse: %v\n", duration, string(tags))

		h.response(writer, tags)
	}
}

var errEmailRequired = errors.New("email param required")
var errPasswordRequired = errors.New("email password required")

func (h *Handler) getToken(bus bus.IBus) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		emails, ok := request.URL.Query()["email"]
		if !ok || len(emails[0]) < 1 {
			h.errResponse(writer, errEmailRequired, http.StatusBadRequest)
			return
		}

		passwords, ok := request.URL.Query()["password"]
		if !ok || len(passwords[0]) < 1 {
			h.errResponse(writer, errPasswordRequired, http.StatusBadRequest)
			return
		}

		data, err := json.Marshal(map[string]interface{}{
			"email":    emails[0],
			"password": passwords[0],
		})
		if err != nil {
			h.errResponse(writer, errTagRequired, http.StatusBadRequest)
			return
		}

		response, err := bus.Request("auth.token", data, 15*time.Second)
		if err != nil {
			h.errResponse(writer, err, 401)
			return
		}

		h.response(writer, response.Data)
	}
}

func (handler *Handler) response(writer http.ResponseWriter, data []byte) {
	writer.Header().Set("Content-Type", "application/json")

	_, err := writer.Write(data)
	if err != nil {
		handler.logger.Error(err)
		return
	}
}

func (handler *Handler) errResponse(writer http.ResponseWriter, err error, statusCode int) {
	writer.WriteHeader(statusCode)
	writer.Header().Set("Content-Type", "application/json")

	resp, err := json.Marshal(ErrResponse{err.Error()})
	if err != nil {
		handler.logger.Error(err)
	}
	_, err = writer.Write(resp)
	if err != nil {
		handler.logger.Error(err)
		return
	}
}
