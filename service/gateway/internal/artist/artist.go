package artist

import (
	"bytes"
	"encoding/json"
	"github.com/sigurniv/metalhead/service/gateway/internal/platform/bus"
	"github.com/sirupsen/logrus"
	"strings"
	"time"
)

type Service struct {
	bus    bus.IBus
	logger *logrus.Logger
}

func NewService(bus bus.IBus, logger *logrus.Logger) *Service {
	return &Service{bus: bus, logger: logger}
}

type Artist struct {
	Name string `json:"name"`
}

func (s *Service) GetTopArtists(tag string) ([]byte, error) {
	names, err := s.getTopArtistNames(tag)
	if err != nil {
		return []byte{}, err
	}

	return s.getArtistsDetail(names)
}

func (s *Service) getArtistsDetail(names string) ([]byte, error) {
	response, err := s.bus.Request("artists.search", []byte(names), 15*time.Second)
	if err != nil {
		return []byte{}, err
	}

	return response.Data, nil
}

func (s *Service) getTopArtistNames(tag string) (string, error) {
	response, err := s.bus.Request("artists.top", []byte(tag), 5*time.Second)
	if err != nil {
		return "", err
	}

	var artists []Artist
	err = json.Unmarshal(response.Data, &artists)
	if err != nil {
		return "", err
	}

	var b bytes.Buffer
	for _, artist := range artists {
		b.WriteString(artist.Name)
		b.WriteString(",")
	}

	return strings.TrimRight(b.String(), ","), nil
}

func (s *Service) GetArtist(name string) ([]byte, error) {
	response, err := s.bus.Request("artist.get", []byte(name), 5*time.Second)
	if err != nil {
		return []byte{}, err
	}

	var artist Artist
	err = json.Unmarshal(response.Data, &artist)
	if err != nil {
		return []byte{}, err
	}

	return response.Data, nil
}

type IArtistService interface {
	GetTopArtists(tag string) ([]byte, error)
	GetArtist(name string) ([]byte, error)
}
