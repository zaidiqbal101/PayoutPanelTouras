import React, { useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';

const DecryptPayload = () => {
  const [payload, setPayload] = useState('');
  const [response, setResponse] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const res = await axios.post('/payout/decrypt', {
        payload: payload
      });
      setResponse(res.data);
    } catch (error) {
      setResponse({ error: error.message });
    }
  };

  // Function to render key-value pairs in a clean format
  const renderKeyValue = (obj, title) => (
    <div className="mb-4">
      <h4 className="text-lg font-medium text-gray-700">{title}</h4>
      <div className="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        {Object.entries(obj).map(([key, value]) => (
          <div key={key} className="flex justify-between py-1">
            <span className="text-sm font-medium text-gray-600 capitalize">{key.replace(/([A-Z])/g, ' $1').trim()}:</span>
            <span className="text-sm text-gray-800">
              {typeof value === 'object' && value !== null ? JSON.stringify(value) : value.toString()}
            </span>
          </div>
        ))}
      </div>
    </div>
  );

  return (
    <div className="max-w-xl mx-auto p-6 bg-white rounded-lg shadow-lg">
      <h2 className="text-xl font-semibold mb-4 text-gray-800">Decrypt Payload</h2>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700">Encrypted Payload</label>
          <textarea
            name="payload"
            value={payload}
            onChange={(e) => setPayload(e.target.value)}
            className="w-full h-32 border border-gray-300 p-2 rounded-md focus:ring-green-500 focus:border-green-500"
            required
          />
        </div>

        <button
          type="submit"
          className="w-full bg-green-600 text-white p-2 rounded-md hover:bg-green-700 transition-colors"
        >
          Decrypt
        </button>
      </form>
<button
  type="button"
  onClick={() => router.visit('/')}
  className="w-full mt-2 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition-colors"
>
  Go to Send Payout
</button>

      {response && (
        <div className="mt-6 bg-gray-50 p-4 rounded-lg shadow-sm">
          <h3 className="font-semibold mb-3 text-lg text-gray-800">Decrypted Response</h3>
          {response.error ? (
            <div className="text-red-600 text-sm">Error: {response.error}</div>
          ) : (
            <>
              {response.decrypted ? (
                (() => {
                  try {
                    const decryptedData = JSON.parse(response.decrypted);
                    return (
                      <>
                        {decryptedData.header && renderKeyValue(decryptedData.header, 'Header')}
                        {decryptedData.userInfo && (
                          <div className="mb-4">
                            <h4 className="text-lg font-medium text-gray-700">User Info</h4>
                            <div className="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                              {decryptedData.userInfo.length === 0 ? (
                                <p className="text-sm text-gray-600">No user info available</p>
                              ) : (
                                decryptedData.userInfo.map((user, index) => (
                                  <div key={index} className="py-1">
                                    <p className="text-sm text-gray-800">User {index + 1}: {JSON.stringify(user)}</p>
                                  </div>
                                ))
                              )}
                            </div>
                          </div>
                        )}
                        {decryptedData.transaction && renderKeyValue(decryptedData.transaction, 'Transaction')}
                      </>
                    );
                  } catch (e) {
                    return <div className="text-red-600 text-sm">Error parsing decrypted data: {e.message}</div>;
                  }
                })()
              ) : (
                <div className="text-sm text-gray-600">No decrypted data available</div>
              )}
            </>
          )}
        </div>
      )}
      
    </div>
  );
};

export default DecryptPayload;