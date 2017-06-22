FROM debian:wheezy

# Install GeoIP
RUN apt-get -y update && apt-get -y install geoip-database libgeoip-dev git-core dh-autoreconf wget zlib1g-dev libcurl4-openssl-dev curl make automake autoconf libtool libreadline-dev libncurses5-dev libpcre3-dev libssl-dev perl libpq-dev

#Install GeoIP2

WORKDIR /usr/src
RUN git clone --recursive https://github.com/maxmind/libmaxminddb
RUN cd libmaxminddb && ./bootstrap && ./configure && make check && make install && ldconfig
RUN git clone https://github.com/leev/ngx_http_geoip2_module.git 

RUN wget https://github.com/maxmind/geoipupdate/releases/download/v2.0.2/geoipupdate-2.0.2.tar.gz
RUN wget https://github.com/alanxz/rabbitmq-c/releases/download/v0.5.2/rabbitmq-c-0.5.2.tar.gz && tar zxvf rabbitmq-c-0.5.2.tar.gz && cd rabbitmq-c-0.5.2 && ./configure && make && make install

RUN tar xzvf geoipupdate-2.0.2.tar.gz && cd geoipupdate-2.0.2 && ./configure && make  && make install

RUN apt-get update \
&& apt-get install -y --no-install-recommends \
curl wget perl make build-essential procps \
libreadline-dev libncurses5-dev libpcre3-dev libssl-dev \
&& rm -rf /var/lib/apt/lists/*

ENV OPENRESTY_VERSION 1.9.7.3
ENV OPENRESTY_PREFIX /opt/openresty
ENV NGINX_PREFIX /opt/openresty/nginx
ENV VAR_PREFIX /var/nginx

# NginX prefix is automatically set by OpenResty to $OPENRESTY_PREFIX/nginx
# look for $ngx_prefix in https://github.com/openresty/ngx_openresty/blob/master/util/configure

RUN cd /root \
&& echo "==> Downloading OpenResty..." \
&& curl -sSL http://openresty.org/download/openresty-${OPENRESTY_VERSION}.tar.gz | tar -xvz \
&& echo "==> Configuring OpenResty..." \
&& cd openresty-* \
&& readonly NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
&& echo "using upto $NPROC threads" \
&& ./configure \
--prefix=$OPENRESTY_PREFIX \
--with-http_geoip_module \
--add-module=/usr/src/ngx_http_geoip2_module \
--with-http_postgres_module --with-http_stub_status_module \
--http-client-body-temp-path=$VAR_PREFIX/client_body_temp \
--http-proxy-temp-path=$VAR_PREFIX/proxy_temp \
--http-log-path=$VAR_PREFIX/access.log \
--error-log-path=$VAR_PREFIX/error.log \
--pid-path=$VAR_PREFIX/nginx.pid \
--lock-path=$VAR_PREFIX/nginx.lock \
--with-luajit \
--with-pcre-jit \
--with-ipv6 \
--with-http_ssl_module \
--without-http_ssi_module \
--without-http_userid_module \
--without-http_uwsgi_module \
--without-http_scgi_module \
-j${NPROC} \
&& echo "==> Building OpenResty..." \
&& make -j${NPROC} \
&& echo "==> Installing OpenResty..." \
&& make install \
&& echo "==> Finishing..." \
&& ln -sf $NGINX_PREFIX/sbin/nginx /usr/local/bin/nginx \
&& ln -sf $NGINX_PREFIX/sbin/nginx /usr/local/bin/openresty \
&& ln -sf $OPENRESTY_PREFIX/bin/resty /usr/local/bin/resty \
&& ln -sf $OPENRESTY_PREFIX/luajit/bin/luajit-* $OPENRESTY_PREFIX/luajit/bin/lua \
&& ln -sf $OPENRESTY_PREFIX/luajit/bin/luajit-* /usr/local/bin/lua \
&& rm -rf /root/ngx_openresty*

RUN \
  apt-get update && \
  apt-get install -y lua5.1 unzip liblua5.1-dev && \
  wget http://luarocks.org/releases/luarocks-2.2.0.tar.gz && \
  tar -xzvf luarocks-2.2.0.tar.gz && \
  rm -f luarocks-2.2.0.tar.gz && \
  cd luarocks-2.2.0 && \
  ./configure --with-lua=/usr && \
  make build && \
  make install && \
  make clean && \
  cd .. && \
  rm -rf luarocks-2.2.0

RUN \
    luarocks install lua-resty-libcjson && \
    luarocks install lua-resty-cookie && \
    luarocks install lua-resty-session && \
    luarocks install lua-resty-template && \
    luarocks install lua-resty-string && \
    luarocks install lua-resty-cors && \
    luarocks install lua-cjson && \
    luarocks install lrexlib-pcre 2.7.2-1 PCRE_LIBDIR=/usr/lib/x86_64-linux-gnu && \
    luarocks install mobile_detect && \
    luarocks install luasocket && \
    luarocks install lzlib

WORKDIR $NGINX_PREFIX/

ONBUILD RUN rm -rf conf/* html/*
ONBUILD COPY nginx $NGINX_PREFIX/

ADD Mobile_Detect.json $NGINX_PREFIX/conf
ADD nginx.conf $NGINX_PREFIX/conf
ADD app.conf $NGINX_PREFIX/conf/conf.d/
ADD kibana.conf $NGINX_PREFIX/conf/conf.d/
ADD grafana.conf $NGINX_PREFIX/conf/conf.d/
ADD elasticsearch.conf $NGINX_PREFIX/conf/conf.d/
ADD cookie.lua $OPENRESTY_PREFIX/lualib/resty/
ADD mobile.lua $OPENRESTY_PREFIX/lualib/resty/
ADD template.lua $OPENRESTY_PREFIX/lualib/resty/

RUN echo "upstream php-upstream { server php:9001; }" > /opt/openresty/nginx/conf/conf.d/upstream.conf

RUN echo "fastcgi_read_timeout 600;" >> /opt/openresty/nginx/conf/fastcgi.conf

# Fix nginx issue with too many nginx variables because of GeoIP (see https://github.com/agentzh/srcache-nginx-module/issues/21)
RUN sed -i 's/mime.types;/mime.types;\n    variables_hash_max_size 1024;/' /opt/openresty/nginx/conf/nginx.conf

ADD geoip/ $NGINX_PREFIX/

# fix permissions if we're running on OSX
WORKDIR /root
ADD fix-permissions.sh /root/fix-permissions.sh
RUN chmod 777 /root/fix-permissions.sh
RUN ./fix-permissions.sh
WORKDIR /var/www/app

CMD ["nginx", "-g", "daemon off; error_log /dev/stderr info;"]
