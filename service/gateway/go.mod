module github.com/sigurniv/metalhead/service/gateway

go 1.13

require (
	github.com/fsnotify/fsnotify v1.4.7 // indirect
	github.com/gorilla/mux v1.6.1
	github.com/hashicorp/hcl v1.0.0 // indirect
	github.com/magiconair/properties v1.8.1 // indirect
	github.com/mitchellh/mapstructure v1.1.2 // indirect
	github.com/nats-io/nats.go v1.9.1
	github.com/patrickmn/go-cache v2.1.0+incompatible
	github.com/pelletier/go-toml v1.6.0 // indirect
	github.com/sirupsen/logrus v1.4.2
	github.com/spf13/afero v1.2.2 // indirect
	github.com/spf13/cast v1.3.1 // indirect
	github.com/spf13/jwalterweatherman v1.1.0 // indirect
	github.com/spf13/pflag v1.0.5 // indirect
	github.com/spf13/viper v1.0.2
	go.uber.org/atomic v1.5.1 // indirect
	go.uber.org/multierr v1.4.0 // indirect
	go.uber.org/zap v1.8.0
	gopkg.in/yaml.v2 v2.2.8 // indirect
)

replace (
	github.com/sigurniv/metalhead => ../../
	github.com/sigurniv/metalhead/service => ../../service
)
