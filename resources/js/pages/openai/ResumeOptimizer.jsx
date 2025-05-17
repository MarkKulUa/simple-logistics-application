import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function ResumeOptimizer() {
    const [result, setResult] = useState('');

    const onFinish = async (values) => {
        const { data } = await axios.post('/api/openai/resume/optimize', values);
        setResult(data.optimized);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Resume Text" name="resume" rules={[{ required: true }]}> <Input.TextArea rows={6} /> </Form.Item>
            <Form.Item label="Job Description" name="job" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Button type="primary" htmlType="submit">Optimize</Button>
            {result && <Typography.Paragraph style={{ marginTop: 16 }}>{result}</Typography.Paragraph>}
        </Form>
    );
}
