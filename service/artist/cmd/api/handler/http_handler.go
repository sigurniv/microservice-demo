package handler

import (
	"encoding/json"
	"errors"
	"github.com/sigurniv/metalhead/service/artist/internal/artist"
	"github.com/sigurniv/metalhead/service/artist/internal/platform/config"
	"github.com/sigurniv/metalhead/service/artist/internal/response"
	"github.com/sigurniv/metalhead/service/artist/internal/yandex"
	"github.com/sirupsen/logrus"
	"net/http"
)

type HttpHandler struct {
	config config.Config
	logger *logrus.Logger
}

func NewHttpHandler(config config.Config, logger *logrus.Logger) *HttpHandler {
	return &HttpHandler{
		config: config,
		logger: logger,
	}
}

var errBadRequest = errors.New("bad request")
var errInternalError = errors.New("internal error")

func (h *HttpHandler) Info() http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		r, err := json.Marshal(response.InfoResponse{
			Data:  response.InfoData{h.config.GetString("service.name")},
			Error: "",
		})

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) SearchArtist(ya yandex.IYandex) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		names, ok := request.URL.Query()["name"]
		if !ok || len(names[0]) < 1 {
			h.errResponse(writer, errBadRequest, http.StatusBadRequest)
			return
		}

		artist, err := ya.SearchArtist(names[0])
		if err != nil {
			h.logger.Error(err)
			h.errResponse(writer, errInternalError, http.StatusInternalServerError)
			return
		}

		r, err := json.Marshal(response.SearchArtistResponse{
			Artist: artist,
		})

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) GetArtist(ya yandex.IYandex) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		ids, ok := request.URL.Query()["id"]
		if !ok || len(ids[0]) < 1 {
			h.errResponse(writer, errBadRequest, http.StatusBadRequest)
			return
		}

		artist, err := ya.GetArtist(ids[0])
		if err != nil {
			h.logger.Println(err)
			h.errResponse(writer, errInternalError, http.StatusInternalServerError)
			return
		}

		r, err := json.Marshal(response.SearchArtistResponse{
			Artist: artist,
		})

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) SearchArtistMultiple(service *artist.Service) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		name, ok := request.URL.Query()["name"]
		if !ok || len(name[0]) < 1 {
			h.errResponse(writer, errBadRequest, http.StatusBadRequest)
			return
		}

		r, err := service.SearchMultiple(name[0])

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) response(writer http.ResponseWriter, data []byte) {
	writer.Header().Set("Content-Type", "application/json")

	_, err := writer.Write(data)
	if err != nil {
		h.logger.Error(err)
		return
	}
}

func (h *HttpHandler) errResponse(writer http.ResponseWriter, err error, statusCode int) {
	writer.WriteHeader(statusCode)
	writer.Header().Set("Content-Type", "application/json")

	resp, err := json.Marshal(response.ErrResponse{err.Error()})
	if err != nil {
		h.logger.Error(err)
	}
	_, err = writer.Write(resp)
	if err != nil {
		h.logger.Error(err)
		return
	}
}
