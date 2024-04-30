# 使用官方 PHP 镜像，带有 Apache 服务器
FROM php:7.4-apache

# 设置环境变量，确保使用 UTF-8 编码
ENV LANG C.UTF-8
ENV LC_ALL C.UTF-8

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# 清理缓存
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 定义一个构建参数，用于接收传递的Apache访问口令
#ARG APACHE_PASSWORD

# 接收构建参数
#ARG APACHE_PASSWORD_ENV

# 将构建参数赋值给环境变量
#ENV APACHE_PASSWORD_ENV=$APACHE_PASSWORD

# 复制站点代码到容器中的 Apache 服务器目录
COPY . /var/www/html

# 更新包列表并安装unzip和apache2-utils工具
RUN apt-get update && apt-get install -y unzip apache2-utils

# 设置工作目录
WORKDIR /var/www/html

# 在当前目录解压xxx.zip文件
#RUN unzip phpweb.zip
#RUN ls -l
RUN chmod 777 albums albums/*

# 使用echo命令来创建.htpasswd文件
#RUN echo admin:$(openssl passwd -aprl $APACHE_PASSWORD) > /etc/apache2/.htpasswd
#RUN echo admin:$APACHE_PASSWORD > /etc/apache2/.htpasswd
# 使用环境变量中的口令创建.htpasswd文件
#RUN echo -bc /etc/apache2/.htpasswd admin "$APACHE_PASSWORD"
#RUN htpasswd -bc /etc/apache2/.htpasswd admin  "$APACHE_PASSWORD"

# 设置.htpasswd文件的权限为644
#RUN chmod 644 /etc/apache2/.htpasswd

# 设置.htpasswd文件的所有者为www-data
#RUN chown www-data:www-data /etc/apache2/.htpasswd

# 配置Apache以使用.htpasswd文件进行身份验证
#RUN echo "<Directory \"/var/www/html\">\\n" \
 #   "AuthType Basic\\n" \
 #   "AuthName \"Restricted Content\"\\n" \
 #   "AuthUserFile /etc/apache2/.htpasswd\\n" \
 #   "Require valid-user\\n" \
 #   "</Directory>" > /etc/apache2/conf-available/auth.conf

# 启用身份验证配置文件
#RUN a2enconf auth

# 暴露 7860 端口
EXPOSE 7860

# 修改 Apache 配置文件以监听 7860 端口
RUN sed -i 's/80/7860/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:7860/' /etc/apache2/sites-available/000-default.conf

# 启动 Apache 服务器
CMD ["apache2-foreground"]
