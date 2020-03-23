module github.com/sigurniv/metalhead/service/artist

go 1.13

require (
	github.com/fsnotify/fsnotify v1.4.7 // indirect
	github.com/golang/protobuf v1.3.4 // indirect
	github.com/gorilla/context v1.1.1 // indirect
	github.com/gorilla/mux v1.6.1
	github.com/hashicorp/hcl v1.0.0 // indirect
	github.com/kr/pretty v0.1.0 // indirect
	github.com/magiconair/properties v1.8.1
	github.com/mitchellh/mapstructure v1.1.2 // indirect
	github.com/nats-io/nats-server/v2 v2.1.4 // indirect
	github.com/nats-io/nats.go v1.9.1
	github.com/pelletier/go-toml v1.6.0 // indirect
	github.com/sirupsen/logrus v1.4.2
	github.com/spf13/afero v1.2.2 // indirect
	github.com/spf13/cast v1.3.1 // indirect
	github.com/spf13/jwalterweatherman v1.1.0 // indirect
	github.com/spf13/pflag v1.0.5 // indirect
	github.com/spf13/viper v1.0.2
	github.com/stretchr/testify v1.5.1
	gopkg.in/check.v1 v1.0.0-20180628173108-788fd7840127 // indirect
	gopkg.in/yaml.v2 v2.2.8 // indirect
)

replace (
	github.com/sigurniv/metalhead => ../../
	github.com/sigurniv/metalhead/service => ../../service
)
