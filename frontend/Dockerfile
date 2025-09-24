FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
# Install both prod and dev dependencies (vite is a devDependency)
RUN npm ci --include=dev --silent || npm install --include=dev --silent

COPY . .

EXPOSE 5174

CMD ["sh", "-c", "npm run dev -- --host 0.0.0.0 --port 5174"]
