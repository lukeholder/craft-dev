FROM ubuntu:latest

ENV TZ=Etc/UTC

RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt-get install --no-install-recommends --yes \
    build-essential \
    ca-certificates \
    composer \
    curl \
    emacs \
    file \
    git \
    kmod \
    less \
    mysql-client \
    php8.1 \
    php8.1-bcmath \
    php8.1-common \
    php8.1-curl \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-mysql \
    php8.1-xml \
    php8.1-zip \
    php-composer-ca-bundle \
    php-composer-pcre \
    php-composer-semver \
    redis-tools \
    zsh \
    && \
    chsh -s /usr/bin/zsh

RUN install -m 0755 -d /etc/apt/keyrings && \
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc && \
    echo "1500c1f56fa9e26b9b8f42452a553675796ade0807cdce11975eb98170b3a570  /etc/apt/keyrings/docker.asc" | sha256sum --check && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
    $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
    tee /etc/apt/sources.list.d/docker.list > /dev/null && \
    apt-get update && \
    apt-get install --no-install-recommends --yes \
    docker-ce \
    docker-ce-cli \
    docker-compose-plugin \
    ;

RUN mkdir -p -m 755 /etc/apt/keyrings && \
    curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg -o /etc/apt/keyrings/githubcli-archive-keyring.gpg \
    && chmod go+r /etc/apt/keyrings/githubcli-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | \
    tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
    apt-get update && \
    apt-get install --no-install-recommends --yes \
    gh \
    ;

RUN curl -L https://fly.io/install.sh | sh

COPY .bashrc /root/.bashrc
COPY .gitconfig /root/.gitconfig
COPY .gitignore_global /root/.gitignore_global
COPY .zshrc /root/.zshrc
