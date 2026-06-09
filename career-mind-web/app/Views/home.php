<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Mind | AI-Based Career Guidance System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-brain me-2"></i>
                <span class="fw-bold">Career</span>Mind
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#components">Components</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="/register" class="btn btn-primary btn-nav">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section hero-extended">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <span class="hero-badge">AI-Powered Career Guidance</span>
                        <h1 class="hero-title mt-3">Shape Your Future with <span class="text-gradient">Career Mind</span></h1>
                        <p class="hero-subtitle lead">
                            An intelligent AI-driven platform that provides personalized career counseling, 
                            CV analysis, and job recommendations for students and fresh graduates.
                        </p>
                        <div class="hero-buttons mt-4">
                            <a href="/register" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-rocket me-2"></i>Start Free Trial
                            </a>
                            <a href="#features" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-play-circle me-2"></i>See How It Works
                            </a>
                        </div>
                        <div class="hero-stats mt-5">
                            <div class="row">
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                    <h3 class="stat-number">AI-Driven</h3>
                                    <p class="stat-text">ML Algorithms</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                    <h3 class="stat-number">Personalized</h3>
                                    <p class="stat-text">Career Paths</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                    <h3 class="stat-number">Smart CV</h3>
                                    <p class="stat-text">Analysis</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container">
                        <div class="hero-image">
                            <div class="floating-card card-1">
                                <i class="fas fa-robot text-primary"></i>
                                <h6>AI Analysis</h6>
                            </div>
                            <div class="floating-card card-2">
                                <i class="fas fa-chart-line text-success"></i>
                                <h6>Career Mapping</h6>
                            </div>
                            <div class="floating-card card-3">
                                <i class="fas fa-file-alt text-warning"></i>
                                <h6>CV Score</h6>
                            </div>
                            <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Career Guidance Illustration" class="img-fluid rounded-4 shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="features-heading text-center mt-5" id="features">
            <span class="section-badge">Core Features</span>
            <h2 class="section-title mt-3 text-dark">Intelligent Career Guidance System</h2>
            <p class="section-subtitle text-muted">
                Powered by a growing AI & ML engine, starting with clean profile data.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section pt-5 pb-5">
        <div class="container">
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-primary">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4>Personalized Career Paths</h4>
                        <p>AI algorithms analyze your skills, interests, and academic background to suggest optimal career trajectories.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> Skill-based matching</li>
                            <li><i class="fas fa-check-circle"></i> Interest alignment</li>
                            <li><i class="fas fa-check-circle"></i> Market demand analysis</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-success">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h4>Smart CV Analysis</h4>
                        <p>Get instant feedback on your CV with AI-powered analysis and improvement suggestions.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> Content evaluation</li>
                            <li><i class="fas fa-check-circle"></i> ATS compatibility</li>
                            <li><i class="fas fa-check-circle"></i> Keyword optimization</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-warning">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h4>Job Recommendations</h4>
                        <p>Receive tailored job suggestions based on your profile, skills, and career aspirations.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle"></i> Role suitability scoring</li>
                            <li><i class="fas fa-check-circle"></i> Company culture fit</li>
                            <li><i class="fas fa-check-circle"></i> Growth opportunity assessment</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Components -->
    <section id="components" class="components-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-badge">System Architecture</span>
                <h2 class="section-title">High-Level System Components</h2>
            </div>
            
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="architecture-diagram">
                        <div class="layer">
                            <h5><i class="fas fa-layer-group me-2"></i>Presentation Layer</h5>
                            <p>User Interface & Web Portal</p>
                        </div>
                        <div class="connector"><i class="fas fa-arrow-down"></i></div>
                        <div class="layer">
                            <h5><i class="fas fa-cogs me-2"></i>Application Layer</h5>
                            <p>AI Engine & Business Logic</p>
                        </div>
                        <div class="connector"><i class="fas fa-arrow-down"></i></div>
                        <div class="layer">
                            <h5><i class="fas fa-database me-2"></i>Data Layer</h5>
                            <p>Database & Storage</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="component-list">
                        <div class="component-item">
                            <div class="component-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div>
                                <h5>User Profile Management</h5>
                                <p class="text-muted">Store and manage academic, skills, and personal data</p>
                            </div>
                        </div>
                        <div class="component-item">
                            <div class="component-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div>
                                <h5>AI/ML Processing Engine</h5>
                                <p class="text-muted">Machine learning models for intelligent recommendations</p>
                            </div>
                        </div>
                        <div class="component-item">
                            <div class="component-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div>
                                <h5>Career Recommendation</h5>
                                <p class="text-muted">Personalized career path suggestions</p>
                            </div>
                        </div>
                        <div class="component-item">
                            <div class="component-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <h5>CV Analysis Module</h5>
                                <p class="text-muted">Automated CV evaluation and feedback</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>