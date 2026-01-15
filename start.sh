#!/bin/bash

# Start both frontend and backend servers

PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"

echo "Starting jquerynativePHPapi servers..."
echo "Project root: $PROJECT_ROOT"

# Start backend server
echo "Starting backend on localhost:3001..."
cd "$PROJECT_ROOT/backend"
php -S localhost:3001 -t public > /tmp/backend.log 2>&1 &
BACKEND_PID=$!
echo "Backend PID: $BACKEND_PID"

# Wait a moment for backend to start
sleep 1

# Start frontend server
echo "Starting frontend on localhost:3000..."
cd "$PROJECT_ROOT/frontend"
php -S localhost:3000 > /tmp/frontend.log 2>&1 &
FRONTEND_PID=$!
echo "Frontend PID: $FRONTEND_PID"

echo ""
echo "✓ Backend running on http://localhost:3001"
echo "✓ Frontend running on http://localhost:3000"
echo ""
echo "Press Ctrl+C to stop both servers"
echo ""

# Cleanup on exit
trap "echo 'Stopping servers...'; kill $BACKEND_PID $FRONTEND_PID 2>/dev/null; wait 2>/dev/null; echo 'Stopped.'" EXIT

# Wait for user interrupt
wait
