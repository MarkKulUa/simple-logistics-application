import { useState } from 'react';
import { Form, Input, Button, Typography } from 'antd';
import axios from 'axios';

export default function SeoBlogWriter() {
    const [blog, setBlog] = useState('');

    const onFinish = async (values) => {
        const { data } = await axios.post('/api/openai/blog/generate', values);
        setBlog(data.blog);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Topic" name="topic" rules={[{ required: true }]}> <Input /> </Form.Item>
            <Form.Item label="Keywords (comma separated)" name="keywords" rules={[{ required: true }]}> <Input /> </Form.Item>
            <Button type="primary" htmlType="submit">Generate Blog</Button>
            {blog && <Typography.Paragraph style={{ marginTop: 16 }}>{blog}</Typography.Paragraph>}
        </Form>
    );
}
