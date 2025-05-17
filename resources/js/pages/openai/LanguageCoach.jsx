import { useState } from 'react';
import { Form, Input, Button, Typography, Select } from 'antd';
import axios from 'axios';

export default function LanguageCoach() {
    const [output, setOutput] = useState('');

    const onFinish = async (values) => {
        const { data } = await axios.post('/api/openai/language/practice', values);
        setOutput(data.response);
    };

    return (
        <Form onFinish={onFinish} layout="vertical">
            <Form.Item label="Message" name="message" rules={[{ required: true }]}> <Input.TextArea rows={4} /> </Form.Item>
            <Form.Item label="Language" name="lang"> <Select options={[{ value: 'English' }, { value: 'Spanish' }, { value: 'German' }]} /> </Form.Item>
            <Button type="primary" htmlType="submit">Practice</Button>
            {output && <Typography.Paragraph style={{ marginTop: 16 }}>{output}</Typography.Paragraph>}
        </Form>
    );
}
