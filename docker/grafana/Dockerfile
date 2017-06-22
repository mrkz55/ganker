FROM grafana/grafana

RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/grafana-server

WORKDIR /go/src/github.com/grafana/grafana

CMD ["go", "run", "build.go", "build"]