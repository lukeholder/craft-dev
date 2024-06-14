
FROM golang:latest as builder

WORKDIR /go/src/github.com/teentix/teentix-site/apex-redirect
ENV CGO_ENABLED=0
COPY go.mod go.mod
RUN go mod download && go mod verify
COPY redirect.go redirect.go
RUN go build -o ./bin/redirect

FROM debian:bullseye-slim
WORKDIR /
COPY --from=builder /go/src/github.com/teentix/teentix-site/apex-redirect/bin/redirect /redirect
ENTRYPOINT ["/redirect"]
