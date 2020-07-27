package bus

import (
	"github.com/nats-io/nats.go"
	"github.com/sirupsen/logrus"
	"time"
)

type Bus struct {
	logger *logrus.Logger
	Nc     *nats.Conn
}

type IBus interface {
	Publish(subj string, data []byte) error
	Request(subj string, data []byte, timeout time.Duration) (*nats.Msg, error)
}

func New(logger *logrus.Logger, uri string) *Bus {
	bus := Bus{logger: logger}
	bus.Nc = bus.Connect(uri)
	return &bus
}

func (bus *Bus) Connect(uri string) *nats.Conn {
	var nc *nats.Conn
	nc, err := nats.Connect(uri)
	if err != nil {
		bus.logger.Fatal("Error establishing connection to NATS:", err)
	}

	bus.logger.Printf("Connected to NATS at: %s", nc.ConnectedUrl())
	return nc
}

func (bus *Bus) Publish(subj string, data []byte) error {
	return bus.Nc.Publish(subj, data)
}

func (bus *Bus) Request(subj string, data []byte, timeout time.Duration) (*nats.Msg, error) {
	requestAt := time.Now()
	response, err := bus.Nc.Request(subj, data, timeout)
	duration := time.Since(requestAt)

	if err != nil {
		bus.logger.Printf("%s scheduled in %+v\nError: %v\n", subj, duration, err)
		return response, err
	}

	bus.logger.Printf("%s scheduled in %+v\nResponse: %v\n", subj, duration, string(response.Data))

	return response, err
}
