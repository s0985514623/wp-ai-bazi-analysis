import React from 'react';
import { Form, Select, Input, Button, Row, Col, Typography } from 'antd';

import '../styles/bazi.css';

const { Title } = Typography;

interface BaziFormProps {
  onSubmit: (formData: FormData) => void;
}

export interface FormData {
  gender: string;
  year: string;
  month: string;
  day: string;
  hour: string;
  country: string;
  city: string;
  analysis_type: string;
}

const BaziForm: React.FC<BaziFormProps> = ({ onSubmit }) => {
  const [form] = Form.useForm<FormData>();

  const handleFinish = (values: FormData) => {
    onSubmit(values);
  };

  // 创建年份选项
  const yearOptions = [];
  for (let year = 1950; year <= 2023; year++) {
    yearOptions.push(
      <Select.Option key={year} value={year.toString()}>{year}年</Select.Option>
    );
  }

  // 创建月份选项
  const monthOptions = [];
  for (let month = 1; month <= 12; month++) {
    monthOptions.push(
      <Select.Option key={month} value={month.toString()}>{month}月</Select.Option>
    );
  }

  // 创建日期选项
  const dayOptions = [];
  for (let day = 1; day <= 31; day++) {
    dayOptions.push(
      <Select.Option key={day} value={day.toString()}>{day}日</Select.Option>
    );
  }

  return (
    <div className="form-wrapper">
      <Title level={2} style={{ textAlign: 'center', marginBottom: '24px' }}>八字命格分析</Title>
      
      <Form<FormData>
        form={form}
        name="bazi-form"
        onFinish={handleFinish}
        layout="vertical"
        requiredMark={false}
        className="antd-bazi-form"
        size="large"
      >
        <Form.Item
          name="gender"
          label="性別"
          rules={[{ required: true, message: '請選擇性別' }]}
        >
          <Select placeholder="請選擇">
            <Select.Option value="male">男</Select.Option>
            <Select.Option value="female">女</Select.Option>
          </Select>
        </Form.Item>

        <Form.Item label="生辰" style={{ marginBottom: '8px' }}>
          <Row gutter={8}>
            <Col span={6}>
              <Form.Item
                name="year"
                rules={[{ required: true, message: '請選擇' }]}
                noStyle
              >
                <Select placeholder="年">
                  {yearOptions}
                </Select>
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item
                name="month"
                rules={[{ required: true, message: '請選擇' }]}
                noStyle
              >
                <Select placeholder="月">
                  {monthOptions}
                </Select>
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item
                name="day"
                rules={[{ required: true, message: '請選擇' }]}
                noStyle
              >
                <Select placeholder="日">
                  {dayOptions}
                </Select>
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item
                name="hour"
                rules={[{ required: true, message: '請選擇' }]}
                noStyle
              >
                <Select placeholder="時辰">
                  <Select.Option value="0">子時 (23:00-1:00)</Select.Option>
                  <Select.Option value="1">丑時 (1:00-3:00)</Select.Option>
                  <Select.Option value="2">寅時 (3:00-5:00)</Select.Option>
                  <Select.Option value="3">卯時 (5:00-7:00)</Select.Option>
                  <Select.Option value="4">辰時 (7:00-9:00)</Select.Option>
                  <Select.Option value="5">巳時 (9:00-11:00)</Select.Option>
                  <Select.Option value="6">午時 (11:00-13:00)</Select.Option>
                  <Select.Option value="7">未時 (13:00-15:00)</Select.Option>
                  <Select.Option value="8">申時 (15:00-17:00)</Select.Option>
                  <Select.Option value="9">酉時 (17:00-19:00)</Select.Option>
                  <Select.Option value="10">戌時 (19:00-21:00)</Select.Option>
                  <Select.Option value="11">亥時 (21:00-23:00)</Select.Option>
                  <Select.Option value="unknown">不知道</Select.Option>
                </Select>
              </Form.Item>
            </Col>
          </Row>
        </Form.Item>

        <Form.Item label="出生地" style={{ marginBottom: '8px' }}>
          <Row gutter={8}>
            <Col span={12}>
              <Form.Item
                name="country"
                rules={[{ required: true, message: '請輸入國家' }]}
                noStyle
              >
                <Input placeholder="國家" />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                name="city"
                rules={[{ required: true, message: '請輸入城市' }]}
                noStyle
              >
                <Input placeholder="城市" />
              </Form.Item>
            </Col>
          </Row>
        </Form.Item>

        <Form.Item
          name="analysis_type"
          label="想了解的項目"
          rules={[{ required: true, message: '請選擇分析項目' }]}
        >
          <Select placeholder="請選擇">
            <Select.Option value="general">總體運勢</Select.Option>
            <Select.Option value="love">感情姻緣</Select.Option>
            <Select.Option value="career">工作事業</Select.Option>
            <Select.Option value="wealth">財運</Select.Option>
            <Select.Option value="health">健康</Select.Option>
          </Select>
        </Form.Item>

        <Form.Item style={{ marginTop: '24px' }}>
          <Button type="primary" htmlType="submit" size="large" block>
            命格分析
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
};

export default BaziForm; 