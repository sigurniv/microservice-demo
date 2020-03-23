package lastfm

import (
	"encoding/json"
	"fmt"
	"github.com/sigurniv/metalhead/service/tag/config"
	"io/ioutil"
	"net/http"
)

type ILastFm interface {
	GetTopArtists(tag string) (artists []Artist, err error)
	GetTags() []string
}

type LastFM struct {
	client  http.Client
	apiKey  string
	baseUrl string
}

func New(client http.Client, config config.Config) *LastFM {
	return &LastFM{
		client:  client,
		apiKey:  config.GetString("lastfm.apiKey"),
		baseUrl: "http://ws.audioscrobbler.com/2.0/",
	}
}

type Artist struct {
	Name string `json:"name"`
	Url  string `json:"url"`
	Mbid string `json:"mbid"`
}

type Image struct {
	Link string `json:"#text"`
	Size string `json:"size"`
}

const sizeMedium = "medium"

type Images []Image

func (i Images) getMedium() Image {
	for _, image := range i {
		if image.Size == sizeMedium {
			return image
		}
	}

	return Image{}
}

func (s *LastFM) GetTags() []string {
	return []string{
		"melodic death metal",
		"thrash metal",
		"alternative metal",
		"avant-garde metal",
		"black metal",
		"viking metal",
		"glam metal",
		"gothic metal",
		"groove metal",
		"dark metal",
		"doom metal",
		"death metal",
		"celtic metal",
		"math metal",
		"neo-classical metal",
		"oriental metal",
		"power metal",
		"pagan metal",
		"post-metal",
		"progressive metal",
		"symphonic power metal",
		"sludge metal",
		"speed metal",
		"stoner metal",
		"folk metal",
		"heavy metal",
		"nu-metal",
		"industrial metal",
		"rap metal",
		"funk metal",
	}
}

func (s *LastFM) GetTopArtists(tag string) (artists []Artist, err error) {
	body, err := s.request("tag.gettopartists", map[string]string{
		"tag":   tag,
		"limit": "30",
	})

	if err != nil {
		return
	}

	var response struct {
		TopArtists struct {
			Artist []Artist `json:"artist"`
		} `json:"topartists"`
	}

	err = json.Unmarshal(body, &response)
	if err != nil {
		return
	}

	return response.TopArtists.Artist, err
}

func (s *LastFM) request(method string, params map[string]string) ([]byte, error) {
	url := fmt.Sprintf("%s?method=%s", s.baseUrl, method)

	request, err := http.NewRequest(http.MethodGet, url, nil)
	if err != nil {
		return []byte{}, err
	}

	request.Header.Set("Content-type", "application/json")

	params["api_key"] = s.apiKey
	params["format"] = "json"

	q := request.URL.Query()
	for k, v := range params {
		q.Add(k, v)
	}
	request.URL.RawQuery = q.Encode()

	resp, err := s.client.Do(request)
	if resp != nil {
		defer resp.Body.Close()
	}
	if err != nil {
		return []byte{}, err
	}

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return []byte{}, err
	}

	return body, err
}
