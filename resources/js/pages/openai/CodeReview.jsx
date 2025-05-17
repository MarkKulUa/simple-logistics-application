import { useState } from 'react';
import { Form, Input, Button, Typography, Select } from 'antd';
import axios from 'axios';

export default function CodeReview() {
    const [result, setResult] = useState('');

    const onFinish = async (values) => {
        const { data } = await axios.post('/api/openai/code/review', values);
        setResult(data.review);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Code" name="code" rules={[{ required: true }]}> <Input.TextArea rows={6} /> </Form.Item>
            <Form.Item label="Language" name="language"> <Select options={[{ value: 'PHP' }, { value: 'JS' }, { value: 'Python' }]} /> </Form.Item>
            <Button type="primary" htmlType="submit">Review</Button>
            {result && <Typography.Paragraph style={{ marginTop: 16 }}>{result}</Typography.Paragraph>}
        </Form>
    );
}
