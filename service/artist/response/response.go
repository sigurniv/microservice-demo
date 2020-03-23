package response

import (
	"github.com/sigurniv/metalhead/service/artist/yandex"
)

type InfoData struct {
	Name string `json:"name"`
}

type InfoResponse struct {
	Data  InfoData `json:"data"`
	Error string   `json:"error"`
}

type SearchArtistResponse struct {
	Artist yandex.Artist `json:"artist"`
}

type SearchArtistMultipleResponse struct {
	Artists []yandex.Artist `json:"artists"`
}

type GetArtistResponse struct {
	Artist yandex.Artist `json:"artist"`
}

type ErrResponse struct {
	Error string `json:"error"`
}
