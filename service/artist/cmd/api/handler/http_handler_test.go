package handler

import (
	"encoding/json"
	"github.com/sigurniv/metalhead/service/artist/artist"
	"github.com/sigurniv/metalhead/service/artist/response"
	"github.com/sigurniv/metalhead/service/artist/yandex"
	"github.com/sirupsen/logrus"
	"io/ioutil"
	"net/http"
	"net/http/httptest"
	"reflect"
	"testing"
)

type MockConfig struct {
}

func (c MockConfig) GetString(key string) string {
	switch key {
	case "service.name":
		return "test"
	}

	return ""
}

func TestInfoHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHttpHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	srv := handler.Info()
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := response.InfoResponse{
		Data:  response.InfoData{"test"},
		Error: "",
	}

	actual := response.InfoResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}

type MockYandex struct {
}

var expectedArtist = yandex.Artist{
	Name:  "Test",
	Id:    "1",
	Cover: yandex.Cover{},
	Image: "",
}

var artists = []yandex.Artist{
	{
		Name:  "test1",
		Id:    "1",
		Cover: yandex.Cover{},
		Image: "",
	},
	{
		Name:  "test2",
		Id:    "2",
		Cover: yandex.Cover{},
		Image: "",
	},
}

func (ya MockYandex) SearchArtist(name string) (yandex.Artist, error) {
	return expectedArtist, nil
}

func (ya MockYandex) SearchArtistMultiple(names []string) []yandex.Artist {
	return artists
}

func (ya MockYandex) GetArtist(id string) (yandex.Artist, error) {
	return expectedArtist, nil
}

func TestSearchArtistHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/expectedArtist/search/?name=test", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHttpHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	mockYandex := MockYandex{}
	srv := handler.SearchArtist(mockYandex)
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := response.SearchArtistResponse{
		Artist: expectedArtist,
	}

	actual := response.SearchArtistResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}

func TestSearchArtistMultipleHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/artists/search/?name=test1,test2", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHttpHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	mockYandex := MockYandex{}
	artistService := artist.NewService(mockYandex)
	srv := handler.SearchArtistMultiple(artistService)
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := response.SearchArtistMultipleResponse{
		Artists: artists,
	}

	actual := response.SearchArtistMultipleResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}
