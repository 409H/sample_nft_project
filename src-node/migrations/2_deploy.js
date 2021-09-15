// migrations/2_deploy.js
// SPDX-License-Identifier: MIT
const SampleNftContract = artifacts.require("SampleNftContract");

module.exports = function(deployer) {
  deployer.deploy(SampleNftContract, "10");
};