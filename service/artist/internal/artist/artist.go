package artist

import (
	"encoding/json"
	"github.com/sigurniv/metalhead/service/artist/internal/response"
	"github.com/sigurniv/metalhead/service/artist/internal/yandex"
	"strings"
)

type IArtistService interface {
	SearchMultiple(names string) ([]byte, error)
	GetArtist(name string) ([]byte, error)
}

type Service struct {
	yandex yandex.IYandex
}

func NewService(ya yandex.IYandex) *Service {
	return &Service{yandex: ya}
}

func (s *Service) SearchMultiple(names string) ([]byte, error) {
	return json.Marshal(response.SearchArtistMultipleResponse{
		Artists: s.yandex.SearchArtistMultiple(strings.Split(names, ",")),
	})
}

func (s *Service) GetArtist(name string) ([]byte, error) {
	artist, err := s.yandex.SearchArtist(name)
	if err != nil {
		return []byte{}, err
	}

	artistDetail, err := s.yandex.GetArtist(artist.Id)
	if err != nil {
		return []byte{}, err
	}

	return json.Marshal(response.GetArtistResponse{
		Artist: artistDetail,
	})
}
