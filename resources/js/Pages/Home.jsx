import Navigation from '@/Components/Navigation';

import { Head } from '@inertiajs/react';
import React from 'react';

export default class Home extends React.Component {
    render() {
        return (
            <>
                <Head title="Home" />
                <Navigation />

                <div className="container mx-auto p-5">
                    <div className="grid grid-cols-4 gap-4">
                        <div className="content col-span-3 grid-col-3 rounded bg-gray-100 shadow-lg p-4">
                            <h2 className="text-xl font-bold mb-6">This is the Home Page.</h2>
                        </div>
                        <div className="sidebar rounded bg-gray-100 shadow-lg p-4">
                            <h2 className="text-xl font-bold mb-6">This is a Side Bar.</h2>
                        </div>
                    </div>
                </div>
            </>
        );
    }
}