FROM docker.elastic.co/logstash/logstash:5.3.1

ENV PATH /usr/share/logstash/bin:$PATH

RUN ls -lahr /usr/share/logstash/bin
RUN echo RAWR


ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["-f", "/usr/share/logstash/pipeline/"]
