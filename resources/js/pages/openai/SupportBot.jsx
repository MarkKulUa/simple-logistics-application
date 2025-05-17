import { useState } from 'react';
import { Form, Input, Button, Typography, Alert, Space } from 'antd';
import axios from 'axios';
import ReactMarkdown from 'react-markdown';

export default function SupportBot() {
    const [messages, setMessages] = useState([
        { role: 'system', content: 'You are a helpful assistant for e-commerce support.' }
    ]);
    const [input, setInput] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

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

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Ask something" required>
                <Input.TextArea
                    rows={3}
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    placeholder="e.g. How can I return my order?"
                />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading} disabled={!input.trim()}>
                    Send
                </Button>
            </Form.Item>

            <Space direction="vertical" style={{ width: '100%' }}>
                {error && <Alert type="error" message={error} showIcon />}

                <Typography.Title level={5}>Conversation History</Typography.Title>
                {messages.filter(m => m.role !== 'system').map((msg, index) => (
                    <div key={index}>
                        <Typography.Text strong>{msg.role === 'user' ? 'You:' : 'AI:'}</Typography.Text>
                        <ReactMarkdown>{msg.content}</ReactMarkdown>
                    </div>
                ))}
            </Space>
        </Form>
    );
}
