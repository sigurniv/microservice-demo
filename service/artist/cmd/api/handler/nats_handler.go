package handler

import (
	"encoding/json"
	"github.com/nats-io/nats.go"
	"github.com/sigurniv/metalhead/service/artist/internal/artist"
	"github.com/sigurniv/metalhead/service/artist/internal/platform/bus"
	"github.com/sigurniv/metalhead/service/artist/internal/response"
	"github.com/sirupsen/logrus"
)

type NatsHandler struct {
	logger *logrus.Logger
	bus    bus.IBus
}

func NewNatsHandler(logger *logrus.Logger, bus bus.IBus) *NatsHandler {
	return &NatsHandler{logger: logger, bus: bus}
}

func (h *NatsHandler) SearchArtistMultiple(service artist.IArtistService) func(m *nats.Msg) {
	return func(m *nats.Msg) {
		tag := string(m.Data)
		h.logger.Printf("Got artists.search request on: %s with: %s", m.Subject, tag)
		reply := m.Reply
		r, err := service.SearchMultiple(tag)

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

func (h *NatsHandler) GetArtist(service artist.IArtistService) func(m *nats.Msg) {
	return func(m *nats.Msg) {
		name := string(m.Data)
		h.logger.Printf("Got artist.get request on: %s with: %s", m.Subject, name)
		reply := m.Reply
		r, err := service.GetArtist(name)

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
