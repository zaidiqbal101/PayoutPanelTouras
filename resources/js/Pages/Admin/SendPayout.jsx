import React, { useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';
import Sidebar from '../../Layouts/sidebar'; // ✅ Correct path

const SendPayout = () => {
  const [form, setForm] = useState({
    operatingSystem: '',
    sessionId: '',
    version: '',
    requestType: '',
    requestSubType: '',
    tranCode: '',
    txnAmt: '',
    id: '',
  });

  const [response, setResponse] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm({ ...form, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      const res = await axios.post('/payout/send', {
        ...form,
        tranCode: parseInt(form.tranCode),
        txnAmt: parseFloat(form.txnAmt),
      });
      setResponse(res.data);
    } catch (error) {
      setResponse({ error: error.message });
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="flex min-h-screen bg-gray-100">
      {/* Sidebar */}
      <Sidebar />

      {/* Main content area */}
      <div className="flex-1 ml-64 p-8">
        <div className="w-full max-w-2xl bg-white rounded-lg shadow-xl p-8 mx-auto">
          <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">
            {/* Send Payout (Encryption) - (Get Load Money List) */}
            Encryption 
          </h2>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {[
                'operatingSystem',
                'sessionId',
                'version',
                'requestType',
                'requestSubType',
                'tranCode',
                'txnAmt',
                'id',
              ].map((field) => (
                <div key={field}>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    {field.replace(/([A-Z])/g, ' $1').trim()}
                  </label>
                  <input
                    type={field === 'tranCode' || field === 'txnAmt' ? 'number' : 'text'}
                    name={field}
                    value={form[field]}
                    onChange={handleChange}
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    required
                  />
                </div>
              ))}
            </div>

            <button
              type="submit"
              disabled={isSubmitting}
              className={`w-full py-3 px-4 rounded-md text-white font-semibold transition duration-200 ${
                isSubmitting
                  ? 'bg-blue-400 cursor-not-allowed'
                  : 'bg-blue-600 hover:bg-blue-700'
              }`}
            >
              {isSubmitting ? (
                <span className="flex items-center justify-center">
                  <svg
                    className="animate-spin h-5 w-5 mr-2 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      className="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      strokeWidth="4"
                    ></circle>
                    <path
                      className="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                  </svg>
                  Submitting...
                </span>
              ) : (
                'Submit'
              )}
            </button>
          </form>

          <button
            type="button"
            onClick={() => router.visit('/payout/decrypt')}
            className="w-full mt-2 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            Go to Decryption Payout Page
          </button>

          {response && (
            <div className="mt-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
              <h3 className="text-lg font-semibold text-gray-800 mb-3">Response</h3>
              <pre className="text-sm text-gray-700 bg-white p-4 rounded-md overflow-auto max-h-64">
                {JSON.stringify(response, null, 2)}
              </pre>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default SendPayout;
  