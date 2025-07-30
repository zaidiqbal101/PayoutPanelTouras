import React, { useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';
import Sidebar from '../../Layouts/Sidebar';

const TourasPayout = () => {
  const [activeTab, setActiveTab] = useState('addBeneficiary');
  const [form, setForm] = useState({
    addBeneficiary: {
      contact_type: '',
      name: '',
      org_name: '',
      email_id: '',
      mobile_no: '',
      me_id: 'AGEN4430031130',
      account_no: '',
      ifsc_code: '',
      account_holder_name: '',
      code: '',
      pan_no: '',
      registration_type: 'Consumer',
      gst_no: '',
      notes: '',
    },
    payoutWithBene: {
      operatingSystem: 'WEB',
      sessionId: 'AGEN4430031130',
      version: '1.0.0',
      mobileNo: '',
      txnAmount: '',
      beneId: '',
      count: 0,
      orderRefNo: '',
      payMode: 'IMPS',
    },
    getBeneList: {
      operatingSystem: 'WEB',
      sessionId: 'AGEN4430031130',
      version: '',
      id: '',
    },
    payoutWithoutBene: {
      operatingSystem: 'WEB',
      sessionId: 'AGEN4430031130',
      version: '1.0.0',
      id: 'AGEN4430031130',
      mobileNo: '',
      txnAmount: '',
      accountNo: '',
      ifscCode: '',
      bankName: '',
      accountHolderName: '',
      txnType: 'IMPS',
      accountType: 'Saving',
      emailId: '',
      orderRefNo: '',
      count: 0,
    },
  });

  const [responses, setResponses] = useState({
    addBeneficiary: null,
    payoutWithBene: null,
    getBeneList: null,
    payoutWithoutBene: null,
  });

  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleChange = (e, section) => {
    const { name, value } = e.target;
    setForm((prev) => ({
      ...prev,
      [section]: { ...prev[section], [name]: value },
    }));
  };

  const handleSubmit = async (e, endpoint, section) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      const res = await axios.post(`/touras/${endpoint}`, form[section]);

      if (res.data.error) {
        setResponses((prev) => ({
          ...prev,
          [section]: { error: res.data.error },
        }));
      } else {
        setResponses((prev) => ({
          ...prev,
          [section]: {
            raw: res.data.response_raw,
            json: res.data.response_json,
          },
        }));
      }
    } catch (error) {
      setResponses((prev) => ({
        ...prev,
        [section]: { error: error.message },
      }));
    } finally {
      setIsSubmitting(false);
    }
  };

  const renderFormFields = (fields, section) => (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
      {fields.map((field) => (
        <div key={field.name}>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            {field.label}
          </label>
          <input
            type={field.type || 'text'}
            name={field.name}
            value={form[section][field.name]}
            onChange={(e) => handleChange(e, section)}
            className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
            required={field.required}
          />
        </div>
      ))}
    </div>
  );

  const renderResponse = () => {
    const response = responses[activeTab];
    if (!response) return null;

    const { error } = response;
    const json = response.json || {};
    const raw = response.raw || {};

    const beneList = json.beneList || raw.beneList || [];

    return (
      <div className="mt-6 bg-white border border-gray-200 p-5 rounded shadow">
        {error && <p className="text-red-600 font-semibold">❌ {error}</p>}
        {json.userMessage && (
          <p className="text-green-600 font-semibold mb-4">✅ {json.userMessage}</p>
        )}

        {/* Bene List Display Only if Current Tab is "getBeneList" */}
        {activeTab === 'getBeneList' && beneList.length > 0 && (
          <>
            <h3 className="text-md font-bold text-gray-800 mb-2">Beneficiary List</h3>
            <ul className="space-y-2 max-h-64 overflow-y-auto">
              {beneList.map((bene, index) => (
                <li key={bene.beneId || index} className="bg-gray-100 p-4 rounded shadow">
                  <p><strong>Name:</strong> {bene.name || bene.beneName}</p>
                  <p><strong>Account No:</strong> {bene.accountNo}</p>
                  <p><strong>IFSC:</strong> {bene.ifscCode}</p>
                  <p><strong>Mobile:</strong> {bene.mobileNo}</p>
                  <p><strong>Email:</strong> {bene.emailId}</p>
                  <p><strong>BeneId:</strong> {bene.beneId}</p>
                  <p><strong>Id:</strong> {bene.id}</p>
                  <p><strong>Status:</strong> {bene.beneStatus}</p>
                  <p><strong>Added On:</strong> {bene.CreationDate}</p>
                </li>
              ))}
            </ul>
          </>
        )}

        {/* Default for non-list responses */}
        {activeTab !== 'getBeneList' && (
          <pre className="text-sm text-gray-800 bg-gray-50 p-3 rounded overflow-auto max-h-64">
            {JSON.stringify(json, null, 2)}
          </pre>
        )}
      </div>
    );
  };

  return (
    <div className="flex min-h-screen bg-gray-100">
      <Sidebar />
      <div className="flex-1 ml-64 p-8">
        <div className="w-full max-w-4xl bg-white rounded-lg shadow-xl p-8 mx-auto">
          <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">
            Touras Payout APIs
          </h2>

          {/* Tabs */}
          <div className="flex border-b mb-6">
            {[
              { id: 'addBeneficiary', label: 'Add Beneficiary' },
              { id: 'payoutWithBene', label: 'Payout with Beneficiary' },
              { id: 'getBeneList', label: 'Get Beneficiary List' },
              { id: 'payoutWithoutBene', label: 'Payout without Beneficiary' },
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`px-4 py-2 font-semibold text-sm ${
                  activeTab === tab.id
                    ? 'border-b-2 border-blue-600 text-blue-600'
                    : 'text-gray-600 hover:text-blue-600'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </div>

          {/* Add Beneficiary Form */}
          {activeTab === 'addBeneficiary' && (
            <form
              onSubmit={(e) => handleSubmit(e, 'add-beneficiary', 'addBeneficiary')}
              className="space-y-6"
            >
              {renderFormFields(
                [
                  { name: 'name', label: 'Contact Name', required: true },
                  { name: 'org_name', label: 'Organization Name' },
                  { name: 'email_id', label: 'Email ID', required: true },
                  { name: 'mobile_no', label: 'Mobile No', required: true },
                  { name: 'me_id', label: 'Merchant ID', required: true },
                  { name: 'account_no', label: 'Account No', required: true },
                  { name: 'ifsc_code', label: 'IFSC Code', required: true },
                  { name: 'account_holder_name', label: 'Account Holder Name', required: true },
                  { name: 'code', label: 'Beneficiary Code' },
                  { name: 'pan_no', label: 'PAN No' },
                  { name: 'gst_no', label: 'GST No' },
                  { name: 'notes', label: 'Notes' },
                ],
                'addBeneficiary'
              )}
              <button
                type="submit"
                disabled={isSubmitting}
                className={`w-full py-3 px-4 rounded-md text-white font-semibold transition duration-200 ${
                  isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
                }`}
              >
                {isSubmitting ? 'Submitting...' : 'Add Beneficiary'}
              </button>
            </form>
          )}

          {/* Payout with Beneficiary Form */}
          {activeTab === 'payoutWithBene' && (
            <form
              onSubmit={(e) => handleSubmit(e, 'payout-with-bene', 'payoutWithBene')}
              className="space-y-6"
            >
              {renderFormFields(
                [
                  { name: 'operatingSystem', label: 'Operating System', required: true },
                  { name: 'sessionId', label: 'Session ID', required: true },
                  { name: 'version', label: 'Version', required: true },
                  { name: 'mobileNo', label: 'Mobile No', required: true },
                  { name: 'txnAmount', label: 'Transaction Amount', type: 'number', required: true },
                  { name: 'beneId', label: 'Beneficiary ID', required: true },
                  { name: 'orderRefNo', label: 'Order Reference No', required: true },
                  { name: 'payMode', label: 'Pay Mode', required: true },
                ],
                'payoutWithBene'
              )}
              <button
                type="submit"
                disabled={isSubmitting}
                className={`w-full py-3 px-4 rounded-md text-white font-semibold transition duration-200 ${
                  isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
                }`}
              >
                {isSubmitting ? 'Submitting...' : 'Process Payout'}
              </button>
            </form>
          )}

          {/* Get Beneficiary List Form */}
          {activeTab === 'getBeneList' && (
            <form
              onSubmit={(e) => handleSubmit(e, 'get-bene-list', 'getBeneList')}
              className="space-y-6"
            >
              {renderFormFields(
                [
                  { name: 'operatingSystem', label: 'Operating System', required: true },
                  { name: 'sessionId', label: 'Session ID', required: true },
                  { name: 'version', label: 'Version', required: true },
                  { name: 'id', label: 'User ID', required: true },
                ],
                'getBeneList'
              )}
              <button
                type="submit"
                disabled={isSubmitting}
                className={`w-full py-3 px-4 rounded-md text-white font-semibold transition duration-200 ${
                  isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
                }`}
              >
                {isSubmitting ? 'Submitting...' : 'Get Beneficiary List'}
              </button>
            </form>
          )}

          {/* Payout without Beneficiary Form */}
          {activeTab === 'payoutWithoutBene' && (
            <form
              onSubmit={(e) => handleSubmit(e, 'payout-without-bene', 'payoutWithoutBene')}
              className="space-y-6"
            >
              {renderFormFields(
                [
                  { name: 'operatingSystem', label: 'Operating System', required: true },
                  { name: 'sessionId', label: 'Session ID', required: true },
                  { name: 'version', label: 'Version', required: true },
                  { name: 'id', label: 'User ID', required: true },
                  { name: 'mobileNo', label: 'Mobile No', required: true },
                  { name: 'txnAmount', label: 'Transaction Amount', type: 'number', required: true },
                  { name: 'accountNo', label: 'Account No', required: true },
                  { name: 'ifscCode', label: 'IFSC Code', required: true },
                  { name: 'bankName', label: 'Bank Name', required: true },
                  { name: 'accountHolderName', label: 'Account Holder Name', required: true },
                  { name: 'txnType', label: 'Transaction Type', required: true },
                  { name: 'accountType', label: 'Account Type', required: true },
                  { name: 'emailId', label: 'Email ID', required: true },
                  { name: 'orderRefNo', label: 'Order Reference No', required: true },
                ],
                'payoutWithoutBene'
              )}
              <button
                type="submit"
                disabled={isSubmitting}
                className={`w-full py-3 px-4 rounded-md text-white font-semibold transition duration-200 ${
                  isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
                }`}
              >
                {isSubmitting ? 'Submitting...' : 'Process Payout'}
              </button>
            </form>
          )}

          {/* <button
            type="button"
            onClick={() => router.visit('/payout/decrypt')}
            className="w-full mt-2 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Go to Decrypt Payload Page
          </button> */}

          {/* Render API response based on active tab */}
          {renderResponse()}
        </div>
      </div>
    </div>
  );
};

export default TourasPayout;
