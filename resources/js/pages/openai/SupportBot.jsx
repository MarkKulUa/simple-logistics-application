import { useState } from 'react';
import { Form, Input, Button, Typography, Alert, Space } from 'antd';
import axios from 'axios';

export default function SupportBot() {
    const [response, setResponse] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const onFinish = async ({ question }) => {
        setError('');
        setResponse('');
        setLoading(true);
        try {
            const { data } = await axios.post('/api/openai/support/ask', { question });
            setResponse(data.answer);
        } catch (err) {
            setError('Something went wrong. Please try again later.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item
                label="Your Question"
                name="question"
                rules={[{ required: true, message: 'Please enter a question' }]}
            >
                <Input.TextArea rows={4} placeholder="e.g. How do I track my order?" />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    Ask
                </Button>
            </Form.Item>

            <Space direction="vertical" style={{ width: '100%' }}>
                {error && <Alert type="error" message={error} />}
                {response && (
                    <Typography.Paragraph style={{ marginTop: 16 }}>
                        <strong>AI Response:</strong> {response}
                    </Typography.Paragraph>
                )}
            </Space>
        </Form>
    );
}
