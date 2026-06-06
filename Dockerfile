FROM python:3.12-slim

RUN apt-get update && apt-get install -y \
    php-cli php-curl php-mbstring \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY 99faucet.php exec.py ./

CMD ["php", "99faucet.php"]
