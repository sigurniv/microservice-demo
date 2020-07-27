# build stage
FROM golang:alpine AS build-env
RUN apk --no-cache add build-base
ADD . /src/service
RUN cd /src/service/cmd/api && go build -o service

FROM alpine
RUN apk -U add ca-certificates
WORKDIR /app/service
COPY --from=build-env /src/service/cmd/api/service /app/service
ADD ./cmd/api/config.yaml /app/service/config.yaml

EXPOSE 8080
CMD ["/app/service/service", "--config", "/app/service"]