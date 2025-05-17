export const menuConfig = [
    { key: '/', label: 'Welcome', path: '/', element: 'Welcome', icon: 'ReadOutlined' },
    { key: '/shop', label: 'Warehouse', path: '/shop', element: 'Shop', icon: 'ShoppingOutlined' },
    { key: '/protected', label: 'Protected', path: '/protected', element: 'Protected', icon: 'FileTextOutlined' },
    {
        key: '/openai',
        label: 'OpenAI Tools',
        path: '/openai',
        icon: 'RobotOutlined',
        children: [
            { key: '/openai/support', label: 'Support Bot', path: '/openai/support', element: 'SupportBot', icon: 'RobotOutlined' },
            { key: '/openai/resume', label: 'Resume Optimizer', path: '/openai/resume', element: 'ResumeOptimizer', icon: 'FileTextOutlined' },
            { key: '/openai/code-review', label: 'Code Review', path: '/openai/code-review', element: 'CodeReview', icon: 'CodeOutlined' },
            { key: '/openai/email', label: 'Email Writer', path: '/openai/email', element: 'EmailWriter', icon: 'MailOutlined' },
            { key: '/openai/blog', label: 'Blog Generator', path: '/openai/blog', element: 'SeoBlogWriter', icon: 'HighlightOutlined' },
            { key: '/openai/product', label: 'Product Describer', path: '/openai/product', element: 'ProductDescriber', icon: 'ShoppingOutlined' },
            { key: '/openai/language', label: 'Language Coach', path: '/openai/language', element: 'LanguageCoach', icon: 'BulbOutlined' },
            { key: '/openai/summarize', label: 'Summarizer', path: '/openai/summarize', element: 'Summarizer', icon: 'ReadOutlined' }
        ]
    }
];
