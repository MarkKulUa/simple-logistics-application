import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function SupportBot() {
    const [response, setResponse] = useState('');

    const onFinish = async ({ question }) => {
        const { data } = await axios.post('/api/openai/support/ask', { question });
        setResponse(data.answer);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Your Question" name="question" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Button type="primary" htmlType="submit">Ask</Button>
            {response && <Typography.Paragraph style={{ marginTop: 16 }}>{response}</Typography.Paragraph>}
        </Form>
    );
}
