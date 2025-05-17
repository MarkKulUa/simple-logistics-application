import { useRef, useEffect, useState } from 'react';
import { Form, Input, Button, Typography, Alert, Space, Card } from 'antd';
import axios from 'axios';
import ReactMarkdown from 'react-markdown';

export default function SupportBot() {
    const [messages, setMessages] = useState([
        { role: 'system', content: 'You are a helpful assistant for e-commerce support.' }
    ]);
    const [input, setInput] = useState('How can I return my order?');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const scrollRef = useRef(null);

    const onFinish = async () => {
        setError('');
        setLoading(true);

        const updatedMessages = [
            ...messages,
            { role: 'user', content: input }
        ];

        try {
            const { data } = await axios.post('/api/openai/support/ask', { messages: updatedMessages });
            if (data.answer) {
                setMessages([...updatedMessages, { role: 'assistant', content: data.answer }]);
                setInput('');
            } else {
                setError('Empty response from AI.');
            }
        } catch (err) {
            setError(err?.response?.data?.error || 'Something went wrong.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        scrollRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    return (
        <div style={{ display: 'flex', flexDirection: 'column' }}>
            {error && (
                <Alert type="error" message={error} showIcon style={{ marginBottom: 12 }} />
            )}

            {messages.length > 1 && <Card title="Conversation History" style={{ flex: 1, display: 'flex', flexDirection: 'column' }}>
                <div>
                    {messages.filter(m => m.role !== 'system').map((msg, index) => (
                        <div key={index} style={{ marginBottom: 12 }}>
                            <Typography.Text strong>
                                {msg.role === 'user' ? 'You:' : 'AI:'}
                            </Typography.Text>
                            <ReactMarkdown>{msg.content}</ReactMarkdown>
                        </div>
                    ))}
                    <div ref={scrollRef} />
                </div>
            </Card>
            }

            <Form onFinish={onFinish} layout="vertical" style={{ marginTop: 16 }}>
                <Form.Item>
                    <Input.TextArea
                        rows={3}
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        placeholder="e.g. How can I return my order?"
                        maxLength={100}
                        showCount={{ max: 100 }}
                    />

                </Form.Item>

                <Form.Item style={{ textAlign: 'right', marginBottom: 0 }}>
                    <Button
                        type="primary"
                        htmlType="submit"
                        loading={loading}
                        disabled={!input.trim()}
                    >
                        Send
                    </Button>
                </Form.Item>
            </Form>
        </div>
    );
}
