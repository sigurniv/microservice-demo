package handler

import (
	"github.com/magiconair/properties/assert"
	"github.com/nats-io/nats.go"
	"github.com/sirupsen/logrus"
	"io/ioutil"
	"testing"
)

type MockBus struct {
	PublishFunc func(subj string, data []byte) error
	PublishFuncData []byte
}

func (bus MockBus) Publish(subj string, data []byte) error {
	return bus.PublishFunc(subj, data)
}

type MockArtistService struct {
	SearchMultipleFunc func (names string) ([]byte, error)
}

func (service MockArtistService) SearchMultiple(names string) ([]byte, error) {
	return service.SearchMultipleFunc(names)
}

func TestNatsHandler_SearchArtistMultiple(t *testing.T) {
	logger := logrus.New()
	logger.Out = ioutil.Discard

	natsHandler := NewNatsHandler(logger)

	artists := []byte("123")
	mockBus := MockBus{}
	mockBus.PublishFunc = func(subj string, data []byte) error {
		mockBus.PublishFuncData = data
		return nil
	}

	mockArtistService := MockArtistService{}
	mockArtistService.SearchMultipleFunc = func (names string) ([]byte, error) {
		return artists, nil
	}

	handler := natsHandler.SearchArtistMultiple(mockBus, mockArtistService)

	m := nats.Msg{}
	handler(&m)

	assert.Equal(t, mockBus.PublishFuncData, artists)

}
