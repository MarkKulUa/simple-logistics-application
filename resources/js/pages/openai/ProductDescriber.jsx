import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function ProductDescriber() {
    const [description, setDescription] = useState('');

    const onFinish = async ({ product }) => {
        const { data } = await axios.post('/api/openai/product/describe', { product });
        setDescription(data.description);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Product Info" name="product" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Button type="primary" htmlType="submit">Generate Description</Button>
            {description && <Typography.Paragraph style={{ marginTop: 16 }}>{description}</Typography.Paragraph>}
        </Form>
    );
}
