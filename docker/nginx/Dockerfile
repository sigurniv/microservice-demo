FROM nginx:stable-alpine
RUN rm /etc/nginx/conf.d/default.conf
COPY nginx.conf /etc/nginx/nginx.conf
COPY mime.types /etc/nginx
COPY gzip.conf /etc/nginx
COPY proxy.conf /etc/nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
