package handler

import (
	"encoding/json"
	"errors"
	"github.com/sigurniv/metalhead/service/tag/config"
	"github.com/sigurniv/metalhead/service/tag/lastfm"
	"github.com/sigurniv/metalhead/service/tag/response"
	"github.com/sigurniv/metalhead/service/tag/tag"
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
			Data:  response.InfoData{Name: h.config.GetString("service.name")},
			Error: "",
		})

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) Tags(lastFm lastfm.ILastFm) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		r, err := tag.List(lastFm)

		if err != nil {
			h.logger.Error(err)
		}

		h.response(writer, r)
	}
}

func (h *HttpHandler) TopArtists(lastFm lastfm.ILastFm) http.HandlerFunc {
	return func(writer http.ResponseWriter, request *http.Request) {
		tags, ok := request.URL.Query()["tag"]
		if !ok || len(tags[0]) < 1 {
			h.errResponse(writer, errBadRequest, http.StatusBadRequest)
			return
		}

		artists, err := tag.TopArtists(lastFm, tags[0])
		if err != nil {
			h.errResponse(writer, errInternalError, http.StatusInternalServerError)
			return
		}

		r, err := json.Marshal(response.TopArtistsResponse{
			Artists: artists,
		})

		if err != nil {
			h.errResponse(writer, errInternalError, http.StatusInternalServerError)
			return
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
	h.logger.Error(err)

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