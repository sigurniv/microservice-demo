package server

type InfoData struct {
	Name string `json:"name"`
}

type InfoResponse struct {
	Data  InfoData `json:"data"`
	Error string   `json:"error"`
}

type Response struct {
	Data  interface{} `json:"data"`
	Error string      `json:"error"`
}

type Tags struct {
	Tags []string `json:"tags"`
}

type TagsResponse struct {
	Data Tags `json:"data"`
}

type ErrResponse struct {
	Error string `json:"error"`
}
