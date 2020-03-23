package handler

import (
	"encoding/json"
	"github.com/sigurniv/metalhead/service/tag/lastfm"
	"github.com/sigurniv/metalhead/service/tag/response"
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

type MockLastFm struct {
}

func (lastFm MockLastFm) GetTopArtistsData(tag string) (artists []lastfm.Artist) {
	artists = append(
		artists,
		lastfm.Artist{Name: "Name1",},
		lastfm.Artist{Name: "Name2",})

	return
}

func (lastFm MockLastFm) GetTopArtists(tag string) (artists []lastfm.Artist, err error) {
	artists = lastFm.GetTopArtistsData(tag)
	return
}

func (lastFm MockLastFm) GetTags() []string {
	return []string{
		"test1",
		"test2",
	}
}

func TestTopArtistsHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/artist/top/?tag=test", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHttpHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	mockLastFm := MockLastFm{}
	srv := handler.TopArtists(mockLastFm)
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := response.TopArtistsResponse{
		Artists: mockLastFm.GetTopArtistsData("test"),
	}

	actual := response.TopArtistsResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}

func TestTTagsHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/artist/Tags/", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHttpHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	mockLastFm := MockLastFm{}
	srv := handler.Tags(mockLastFm)
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := response.TagsResponse{
		Data: response.Tags{mockLastFm.GetTags()},
	}

	actual := response.TagsResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}
