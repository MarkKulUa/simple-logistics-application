import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function Summarizer() {
    const [summary, setSummary] = useState('');

    const onFinish = async ({ text }) => {
        const { data } = await axios.post('/api/openai/summarize', { text });
        setSummary(data.summary);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Long Text" name="text" rules={[{ required: true }]}> <Input.TextArea rows={6} /> </Form.Item>
            <Button type="primary" htmlType="submit">Summarize</Button>
            {summary && <Typography.Paragraph style={{ marginTop: 16 }}>{summary}</Typography.Paragraph>}
        </Form>
    );
}
