package server

import (
	"encoding/json"
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

type MockBus struct {
	PublishFunc     func(subj string, data []byte) error
	PublishFuncData []byte
}

func (bus MockBus) Publish(subj string, data []byte) error {
	return bus.PublishFunc(subj, data)
}

func TestInfoHandler(t *testing.T) {
	req, err := http.NewRequest("GET", "/", nil)
	if err != nil {
		t.Fatal(err)
	}

	logger := logrus.New()
	logger.Out = ioutil.Discard
	handler := NewHandler(MockConfig{}, logger)

	rr := httptest.NewRecorder()
	srv := handler.info()
	srv.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := InfoResponse{
		Data:  InfoData{"test"},
		Error: "",
	}

	actual := InfoResponse{}
	err = json.Unmarshal(rr.Body.Bytes(), &actual)
	if err != nil {
		t.Error("Error unmarshalling response")
	}

	if !reflect.DeepEqual(expected, actual) {
		t.Errorf("handler returned unexpected body: got %v want %v",
			actual, expected)
	}
}
