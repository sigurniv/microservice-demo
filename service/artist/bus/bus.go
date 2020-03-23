package bus

import (
	"github.com/nats-io/nats.go"
	"github.com/sirupsen/logrus"
)

type Bus struct {
	logger *logrus.Logger
	Nc     *nats.Conn
}

type IBus interface {
	Publish(subj string, data []byte) error
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
