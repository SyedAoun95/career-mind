<?php

namespace App\Services;

class LearningPathAdvisor
{
    /**
     * Each track lists the skill keywords it covers plus the learning resource.
     * Keywords are matched as whole words against each missing skill, longest
     * first, so "power bi" beats "bi" and tech skills no longer fall through to
     * a marketing default.
     */
    protected static array $tracks = [
        'programming' => [
            'keywords' => ['python', 'java', 'javascript', 'js', 'typescript', 'c++', 'c#', 'php', 'go', 'ruby', 'rust', 'kotlin', 'swift', 'django', 'flask', 'fastapi', 'react', 'node', 'node.js', 'spring', 'rest api', 'api', 'async programming', 'asyncio', 'algorithms', 'clean code'],
            'track' => 'Software Engineering',
            'summary' => 'Strengthen your programming foundations by building and shipping small projects.',
            'resource' => ['url' => 'https://www.freecodecamp.org/learn', 'label' => 'freeCodeCamp: Coding Curriculum'],
        ],
        'devops' => [
            'keywords' => ['git', 'docker', 'kubernetes', 'linux', 'ci/cd', 'aws', 'azure', 'gcp', 'terraform', 'ansible', 'jenkins', 'devops', 'deployment automation', 'azure devops', 'cmake', 'celery', 'redis'],
            'track' => 'DevOps & Tooling',
            'summary' => 'Learn version control, containers, and cloud deployment through hands-on labs.',
            'resource' => ['url' => 'https://roadmap.sh/devops', 'label' => 'roadmap.sh: DevOps Path'],
        ],
        'data' => [
            'keywords' => ['sql', 'mysql', 'postgresql', 'postgres', 'excel', 'power bi', 'tableau', 'pandas', 'data analysis', 'data analytics', 'dashboards', 'data visualization', 'data cleaning', 'data modeling', 'data pipelines', 'dbt', 'airflow', 'data warehousing', 'reporting', 'jupyter'],
            'track' => 'Data Analytics',
            'summary' => 'Practice querying, cleaning, and visualising data to tell clear stories with numbers.',
            'resource' => ['url' => 'https://www.kaggle.com/learn', 'label' => 'Kaggle: Data Science Courses'],
        ],
        'ai' => [
            'keywords' => ['ml', 'ai', 'machine learning', 'deep learning', 'deep learning basics', 'deep learning fundamentals', 'nlp', 'tensorflow', 'pytorch', 'sklearn', 'computer vision basics', 'clustering', 'anomaly detection', 'cross validation', 'data labeling', 'model', 'ai automation'],
            'track' => 'AI / Machine Learning',
            'summary' => 'Build intuition for models and train your first ML pipelines on real datasets.',
            'resource' => ['url' => 'https://www.coursera.org/learn/machine-learning', 'label' => 'Coursera: Machine Learning'],
        ],
        'security' => [
            'keywords' => ['security', 'cybersecurity', 'splunk', 'siem', 'burp', 'burp suite', 'phishing', 'soc', 'firewall', 'cloud security', 'api security', 'access control', 'compliance', 'crowdstrike', 'auditd', 'incident response', 'azure ad'],
            'track' => 'Cybersecurity',
            'summary' => 'Learn threat detection, secure configuration, and incident response fundamentals.',
            'resource' => ['url' => 'https://www.cybrary.it/', 'label' => 'Cybrary: Security Training'],
        ],
        'design' => [
            'keywords' => ['figma', 'adobe xd', 'sketch', 'ui', 'ux', 'wireframes', 'prototyping', 'design', 'typography', 'color theory', 'accessibility', 'information architecture', 'design audits', 'after effects', 'motion', 'canva'],
            'track' => 'UI / UX Design',
            'summary' => 'Practice wireframing, prototyping, and usability testing for user-centred products.',
            'resource' => ['url' => 'https://www.coursera.org/professional-certificates/google-ux-design', 'label' => 'Google UX Design Certificate'],
        ],
        'marketing' => [
            'keywords' => ['seo', 'content', 'content strategy', 'content marketing', 'copywriting', 'brand', 'brand voice', 'growth', 'digital growth', 'campaign', 'campaign strategy', 'social', 'social media', 'analytics', 'google analytics', 'conversion optimization', 'affiliate marketing', 'hubspot', 'mailchimp', 'meta ads'],
            'track' => 'Digital Marketing',
            'summary' => 'Plan multi-channel campaigns and measure engagement, conversion, and growth.',
            'resource' => ['url' => 'https://grow.google/learn/marketing/', 'label' => 'Google: Digital Marketing'],
        ],
    ];

    public static function recommend(array $missingSkills): array
    {
        $normalizedSkills = array_values(array_filter(array_map('trim', $missingSkills)));
        $suggestions = [];

        foreach ($normalizedSkills as $skill) {
            $suggestions[] = self::suggestionFor($skill);
            if (count($suggestions) >= 3) {
                break;
            }
        }

        if (empty($suggestions)) {
            $suggestions[] = [
                'skill' => 'Build your profile',
                'track' => 'Getting Started',
                'summary' => 'Add skills and interests to your profile to unlock tailored learning paths.',
                'resource' => ['url' => 'https://www.coursera.org/', 'label' => 'Coursera: Browse Courses'],
            ];
        }

        return $suggestions;
    }

    private static function suggestionFor(string $skill): array
    {
        $template = self::trackFor(strtolower($skill));

        if ($template === null) {
            // Skill-specific fallback — never a generic marketing default.
            return [
                'skill' => $skill,
                'track' => 'Skill Builder',
                'summary' => sprintf('Build hands-on projects with %s and follow a structured course to close this gap.', $skill),
                'resource' => [
                    'url' => 'https://www.coursera.org/search?query=' . urlencode($skill),
                    'label' => sprintf('Find "%s" courses on Coursera', $skill),
                ],
            ];
        }

        return [
            'skill' => $skill,
            'track' => $template['track'],
            'summary' => $template['summary'],
            'resource' => $template['resource'],
        ];
    }

    /**
     * Returns the best-matching track for a skill, or null if none match.
     * Longer keywords are tried first so multi-word skills win over substrings.
     */
    private static function trackFor(string $skillLower): ?array
    {
        $best = null;
        $bestLen = 0;

        foreach (self::$tracks as $template) {
            foreach ($template['keywords'] as $keyword) {
                if (strlen($keyword) <= $bestLen) {
                    continue;
                }
                // Whole-word / phrase match so "ai" doesn't match "email", etc.
                $pattern = '/(?<![a-z0-9])' . preg_quote($keyword, '/') . '(?![a-z0-9])/i';
                if (preg_match($pattern, $skillLower)) {
                    $best = $template;
                    $bestLen = strlen($keyword);
                }
            }
        }

        return $best;
    }
}
