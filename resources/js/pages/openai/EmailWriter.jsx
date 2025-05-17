import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function EmailWriter() {
    const [email, setEmail] = useState('');

    const onFinish = async (values) => {
        const { data } = await axios.post('/api/openai/email/generate', values);
        setEmail(data.email);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Lead Info" name="lead" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Form.Item label="Your Offer" name="offer" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Button type="primary" htmlType="submit">Generate Email</Button>
            {email && <Typography.Paragraph style={{ marginTop: 16 }}>{email}</Typography.Paragraph>}
        </Form>
    );
}
