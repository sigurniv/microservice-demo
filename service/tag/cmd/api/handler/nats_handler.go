package handler

import (
	"encoding/json"
	"github.com/nats-io/nats.go"
	"github.com/sigurniv/metalhead/service/tag/internal/lastfm"
	"github.com/sigurniv/metalhead/service/tag/internal/platform/bus"
	"github.com/sigurniv/metalhead/service/tag/internal/response"
	"github.com/sigurniv/metalhead/service/tag/internal/tag"
	"github.com/sirupsen/logrus"
)

type NatsHandler struct {
	logger *logrus.Logger
	bus    bus.IBus
}

func NewNatsHandler(logger *logrus.Logger, bus bus.IBus) *NatsHandler {
	return &NatsHandler{logger: logger, bus: bus}
}

func (h *NatsHandler) Tags(lastFm lastfm.ILastFm) func(m *nats.Msg) {
	return func(m *nats.Msg) {
		h.logger.Printf("Got tag.tags request on: %s", m.Subject)
		reply := m.Reply

		r, err := tag.List(lastFm)

		if err != nil {
			h.errResponse(err, reply)
			return
		}

		err = h.bus.Publish(m.Reply, r)
		if err != nil {
			h.errResponse(err, reply)
			return
		}
	}
}

func (h *NatsHandler) TopArtists(lastFm lastfm.ILastFm) func(m *nats.Msg) {
	return func(m *nats.Msg) {
		data := string(m.Data)
		h.logger.Printf("Got artists.top request on: %s with: %s", m.Subject, data)

		artists, err := tag.TopArtists(lastFm, data)

		reply := m.Reply
		if err != nil {
			h.errResponse(err, reply)
			return
		}

		r, err := json.Marshal(artists)
		if err != nil {
			h.errResponse(err, reply)
			return
		}

		err = h.bus.Publish(m.Reply, r)
		if err != nil {
			h.logger.Error(err)
		}
	}
}

func (h *NatsHandler) errResponse(err error, reply string) {
	h.logger.Error(err)

	resp, err := json.Marshal(response.ErrResponse{err.Error()})
	if err != nil {
		h.logger.Error(err)
	}

	err = h.bus.Publish(reply, resp)
	if err != nil {
		h.logger.Error(err)
	}
}
