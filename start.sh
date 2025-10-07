#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   PGold Application Startup Script${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED} Error: Docker is not running!${NC}"
    echo "Please start Docker and try again."
    exit 1
fi

echo -e "${YELLOW} Building Docker images...${NC}"
docker-compose build

if [ $? -ne 0 ]; then
    echo -e "${RED} Error: Failed to build Docker images${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW} Starting services...${NC}"
echo "   - PostgreSQL Database (port 5432)"
echo "   - Laravel Application (port 8000)"
echo "   - Queue Worker (background jobs)"
echo ""

docker-compose up -d

if [ $? -ne 0 ]; then
    echo -e "${RED} Error: Failed to start services${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW} Waiting for database to be ready...${NC}"
sleep 5

echo ""
echo -e "${YELLOW} Running migrations...${NC}"
docker-compose exec pgold_app php artisan migrate --force

echo ""
echo -e "${YELLOW} Seeding rates...${NC}"
docker-compose exec pgold_app php artisan db:seed --class=RateSeeder

echo ""
echo -e "${YELLOW} Generating application key...${NC}"
docker-compose exec pgold_app php artisan key:generate

echo ""
echo -e "${YELLOW}  Clearing caches...${NC}"
docker-compose exec pgold_app php artisan config:clear
docker-compose exec pgold_app php artisan cache:clear
docker-compose exec pgold_app php artisan route:clear

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}    Application Started Successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${GREEN} Application URL: http://localhost:8000${NC}"
echo -e "${GREEN} Database: localhost:5432${NC}"
echo ""
echo -e "${YELLOW}Useful Commands:${NC}"
echo "  View logs:        docker-compose logs -f"
echo "  Stop services:    docker-compose down"
echo "  Restart services: docker-compose restart"
echo "  Enter container:  docker-compose exec pgold_app sh"
echo ""
echo -e "${YELLOW}Queue Worker Status:${NC}"
docker-compose ps pgold_queue
echo ""
echo -e "${GREEN}Press Ctrl+C to stop viewing logs, or run 'docker-compose logs -f' to follow logs${NC}"
