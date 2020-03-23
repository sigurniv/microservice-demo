package tag

import (
	"encoding/json"
	"github.com/sigurniv/metalhead/service/tag/lastfm"
	"github.com/sigurniv/metalhead/service/tag/response"
)

func List(lastFm lastfm.ILastFm) ([]byte, error) {
	return json.Marshal(response.TagsResponse{
		Data: struct {
			Tags []string `json:"tags"`
		}{lastFm.GetTags()},
	})
}

func TopArtists(lastFm lastfm.ILastFm, tag string)([]lastfm.Artist, error) {
	artists, err := lastFm.GetTopArtists(tag)
	if err != nil {
		return []lastfm.Artist{}, err
	}

	return artists, err
}
