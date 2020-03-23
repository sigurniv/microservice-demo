package yandex

import (
	"encoding/json"
	"errors"
	"github.com/sigurniv/metalhead/service/artist/restclient"
	"github.com/sirupsen/logrus"
	"io/ioutil"
	"net/http"
	"sort"
	"strconv"
	"strings"
)

type IYandex interface {
	SearchArtist(name string) (Artist, error)
	GetArtist(id string) (Artist, error)
	SearchArtistMultiple(names []string) []Artist
}

type Yandex struct {
	client  restclient.HTTPClient
	logger  *logrus.Logger
	baseUrl string
}

func New(client restclient.HTTPClient, logger *logrus.Logger) *Yandex {
	return &Yandex{
		client:  client,
		logger:  logger,
		baseUrl: "https://music.yandex.ru/handlers/",
	}
}

const artistSearchUrl = "https://music.yandex.ru/handlers/music-search.jsx"
const artistDetailUrl = "https://music.yandex.ru/handlers/artist.jsx?lang=ru"

var errNotFound = errors.New("artist not found")

type Cover struct {
	Uri string `json:"uri"`
}

type Description struct {
	Text string `json:"text"`
}

type Artist struct {
	Name        string      `json:"name"`
	Id          string      `json:"id"`
	Cover       Cover       `json:"cover"`
	Image       string      `json:"image"`
	Description Description `json:"description"`
}

type ArtistDetail struct {
	Name        string      `json:"name"`
	Id          int         `json:"id"`
	Cover       Cover       `json:"cover"`
	Image       string      `json:"image"`
	Description Description `json:"description"`
}

func (ad *ArtistDetail) toArtist() Artist {
	return Artist{
		Name:        ad.Name,
		Id:          strconv.Itoa(ad.Id),
		Cover:       ad.Cover,
		Image:       ad.Image,
		Description: ad.Description,
	}
}

const imageFormat = "400x400"

func (a *Artist) getImage() string {
	return strings.Replace(a.Cover.Uri, "%%", imageFormat, 1)
}

func (a *ArtistDetail) getImage() string {
	return strings.Replace(a.Cover.Uri, "%%", imageFormat, 1)
}

func (ya *Yandex) SearchArtist(name string) (Artist, error) {
	body, err := ya.request(artistSearchUrl, map[string]string{
		"text": name,
		"type": "artists",
	})

	if err != nil {
		return Artist{}, err
	}

	var response struct {
		Artists struct {
			Items []ArtistDetail `json:"items"`
		} `json:"artists"`
	}

	err = json.Unmarshal(body, &response)
	if err != nil {
		return Artist{}, err
	}

	if len(response.Artists.Items) == 0 {
		return Artist{}, errNotFound
	}

	artist := response.Artists.Items[0]
	artist.Image = artist.getImage()
	return artist.toArtist(), nil
}

func (ya *Yandex) GetArtist(id string) (Artist, error) {
	body, err := ya.request(artistDetailUrl, map[string]string{
		"artist": id,
	})

	if err != nil {
		return Artist{}, err
	}

	var response struct {
		Artist Artist `json:"artist"`
	}

	err = json.Unmarshal(body, &response)
	if err != nil {
		return Artist{}, err
	}

	if response.Artist.Name == "" {
		return Artist{}, errNotFound
	}

	artistDetail := response.Artist
	artistDetail.Image = artistDetail.getImage()
	return artistDetail, nil
}

func (ya *Yandex) SearchArtistMultiple(names []string) []Artist {
	artists := make([]Artist, 0, len(names))
	results := make(chan Artist, len(names))
	semaphoreChan := make(chan struct{}, 50)

	for _, name := range names {
		go func(name string, result chan Artist) {
			semaphoreChan <- struct{}{}

			artist, err := ya.SearchArtist(name)
			if err != nil {
				ya.logger.Println(err)
			}

			results <- artist
			<-semaphoreChan
		}(name, results)
	}

	for i := 0; i < len(names); i++ {
		artist := <-results
		if artist.Name != "" {
			artists = append(artists, artist)
		}
	}

	artistNameMap := map[string]int{}
	for key, name := range names {
		artistNameMap[strings.ToLower(name)] = key
	}

	sort.Slice(artists, func(i, j int) bool {
		return artistNameMap[strings.ToLower(artists[i].Name)] < artistNameMap[strings.ToLower(artists[j].Name)]
	})

	return artists
}

func (ya *Yandex) request(url string, params map[string]string) ([]byte, error) {
	request, err := http.NewRequest(http.MethodGet, url, nil)
	if err != nil {
		return []byte{}, err
	}

	request.Header.Set("Content-type", "application/json")

	q := request.URL.Query()
	for k, v := range params {
		q.Add(k, v)
	}
	request.URL.RawQuery = q.Encode()

	ya.logger.Printf("Requesting url : %s", request.URL)
	resp, err := ya.client.Do(request)
	ya.logger.Printf("Done requesting url : %s", request.URL)
	if err != nil {
		return []byte{}, err
	}

	if resp != nil {
		defer resp.Body.Close()
	}

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return []byte{}, err
	}

	return body, err
}
