const canvas = document.getElementById('game-canvas');
const ctx = canvas.getContext('2d');
const scoreElement = document.getElementById('score');
const gameOverElement = document.getElementById('game-over');
const restartBtn = document.getElementById('restart-btn');

// Game variables
let bird = {
    x: 50,
    y: canvas.height / 2,
    velocity: 0,
    gravity: 0.6,
    jump: -12
};

let pipes = [];
let score = 0;
let gameRunning = true;

// Pipe settings
const pipeWidth = 50;
const pipeGap = 150;
const pipeSpeed = 2;

// Bird drawing
function drawBird() {
    ctx.fillStyle = '#FFD700';
    ctx.fillRect(bird.x, bird.y, 20, 20);
}

// Pipe drawing
function drawPipes() {
    ctx.fillStyle = '#228B22';
    pipes.forEach(pipe => {
        ctx.fillRect(pipe.x, 0, pipeWidth, pipe.topHeight);
        ctx.fillRect(pipe.x, canvas.height - pipe.bottomHeight, pipeWidth, pipe.bottomHeight);
    });
}

// Update bird position
function updateBird() {
    bird.velocity += bird.gravity;
    bird.y += bird.velocity;
}

// Generate pipes
function generatePipe() {
    const topHeight = Math.random() * (canvas.height - pipeGap - 50) + 50;
    const bottomHeight = canvas.height - topHeight - pipeGap;
    pipes.push({
        x: canvas.width,
        topHeight: topHeight,
        bottomHeight: bottomHeight,
        passed: false
    });
}

// Update pipes
function updatePipes() {
    pipes.forEach(pipe => {
        pipe.x -= pipeSpeed;
    });

    // Remove off-screen pipes
    pipes = pipes.filter(pipe => pipe.x + pipeWidth > 0);

    // Generate new pipes
    if (pipes.length === 0 || pipes[pipes.length - 1].x < canvas.width - 200) {
        generatePipe();
    }
}

// Collision detection
function checkCollision() {
    // Bird hits ground or ceiling
    if (bird.y < 0 || bird.y + 20 > canvas.height) {
        return true;
    }

    // Bird hits pipes
    for (let pipe of pipes) {
        if (bird.x < pipe.x + pipeWidth && bird.x + 20 > pipe.x) {
            if (bird.y < pipe.topHeight || bird.y + 20 > canvas.height - pipe.bottomHeight) {
                return true;
            }
        }
    }

    return false;
}

// Update score
function updateScore() {
    pipes.forEach(pipe => {
        if (!pipe.passed && pipe.x + pipeWidth < bird.x) {
            pipe.passed = true;
            score++;
            scoreElement.textContent = `Score: ${score}`;
        }
    });
}

// Game loop
function gameLoop() {
    if (!gameRunning) return;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    updateBird();
    updatePipes();
    updateScore();

    if (checkCollision()) {
        gameRunning = false;
        gameOverElement.style.display = 'block';
        return;
    }

    drawBird();
    drawPipes();

    requestAnimationFrame(gameLoop);
}

// Jump function
function jump() {
    if (gameRunning) {
        bird.velocity = bird.jump;
    }
}

// Event listeners
document.addEventListener('keydown', (e) => {
    if (e.code === 'Space') {
        e.preventDefault();
        jump();
    }
});

canvas.addEventListener('click', jump);

// Restart game
restartBtn.addEventListener('click', () => {
    bird.y = canvas.height / 2;
    bird.velocity = 0;
    pipes = [];
    score = 0;
    scoreElement.textContent = 'Score: 0';
    gameRunning = true;
    gameOverElement.style.display = 'none';
    gameLoop();
});

// Start game
generatePipe();
gameLoop();
