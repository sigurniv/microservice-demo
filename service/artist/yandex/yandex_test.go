package yandex

import (
	"bytes"
	"github.com/sirupsen/logrus"
	"github.com/stretchr/testify/assert"
	"io/ioutil"
	"net/http"
	"strings"
	"testing"
)

func TestArtistGetImage(t *testing.T) {
	artist := Artist{
		Name:  "",
		Id:    "0",
		Cover: Cover{Uri: "avatars.yandex.net/get-music-content/33216/4ef50dd2.a.2467744-2/%%"},
		Image: "",
	}

	image := artist.getImage()

	expected := strings.Replace("avatars.yandex.net/get-music-content/33216/4ef50dd2.a.2467744-2/%%", "%%", imageFormat, 1)
	if image != expected {
		t.Errorf("unexpected image uri: got %v want %v",
			image, expected)
	}
}

type MockHTTPClient struct {
	DoFunc    func(req *http.Request) (*http.Response, error)
	DoCounter int
}

func (c *MockHTTPClient) Do(req *http.Request) (*http.Response, error) {
	return c.DoFunc(req)
}

func TestSearchArtist(t *testing.T) {
	logger := logrus.New()
	logger.Out = ioutil.Discard

	client := &MockHTTPClient{DoFunc: func(req *http.Request) (response *http.Response, err error) {
		json := `{"artists": {"items": [{"name": "test1", "id": 1}, {"name": "test2", "id": 2}]}}`
		r := ioutil.NopCloser(bytes.NewReader([]byte(json)))
		return &http.Response{
			StatusCode: 200,
			Body:       r,
		}, nil
	}}

	ya := New(client, logger)
	artist, err := ya.SearchArtist("test1")

	assert.Nil(t, err)

	want := Artist{
		Name:  "test1",
		Id: "1",
	}
	assert.Equal(t, want, artist)
}

func TestSearchArtistMultiple(t *testing.T) {
	logger := logrus.New()
	logger.Out = ioutil.Discard

	client := &MockHTTPClient{}
	doFunc := func(req *http.Request) (response *http.Response, err error) {
		json := ""
		switch client.DoCounter {
		case 0:
			json = `{"artists": {"items": [{"name": "test1", "id": 1}]}}`
		case 1:
			json = `{"artists": {"items": [{"name": "test2", "id": 2}]}}`
		}

		r := ioutil.NopCloser(bytes.NewReader([]byte(json)))
		client.DoCounter++
		return &http.Response{
			StatusCode: 200,
			Body:       r,
		}, nil
	}

	client.DoFunc = doFunc

	ya := New(client, logger)
	artists := ya.SearchArtistMultiple([]string{"test1", "test2"})

	assert.Len(t, artists, 2)
	assert.Equal(t, Artist{Name:  "test1", Id: "1"}, artists[0])
	assert.Equal(t, Artist{Name:  "test2", Id: "2"}, artists[1])
	assert.Equal(t, client.DoCounter, 2)
}
