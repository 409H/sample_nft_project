// SPDX-License-Identifier: MIT
pragma solidity ^0.8.2;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721Enumerable.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Counters.sol";

contract SampleNftContract is ERC721, ERC721Enumerable, Ownable {
    using Counters for Counters.Counter;
    uint immutable maxSupply;

    Counters.Counter private _tokenIdCounter;

    constructor(uint _maxSupply) ERC721("Sample NFT Project", "SNP") {
        maxSupply = _maxSupply - 1;
    }

    function _baseURI() internal pure override returns (string memory) {
        return "https://harrydenley.com/assets/nft/samplenftproject/";
    }
    
    function mint() public payable {
        require(_tokenIdCounter.current() <= maxSupply, "Sold out"); // NFT project sold out
        require(msg.value == 0.01 ether, "Incorrect amount"); // Cost to mint 1 NFT is 0.01 ETH (10000000000000000 wei)
        
        _safeMint(msg.sender, _tokenIdCounter.current());
        _tokenIdCounter.increment();
    }
    
    function withdraw() public onlyOwner { // Owner can withdraw all the eth that was paid to mint NFTs
        address payable recipient = payable(address(msg.sender));
        recipient.transfer(address(this).balance);
    }

    function safeMint(address to) public onlyOwner {
        _safeMint(to, _tokenIdCounter.current());
        _tokenIdCounter.increment();
    }

    // The following functions are overrides required by Solidity.

    function _beforeTokenTransfer(address from, address to, uint256 tokenId)
        internal
        override(ERC721, ERC721Enumerable)
    {
        super._beforeTokenTransfer(from, to, tokenId);
    }

    function supportsInterface(bytes4 interfaceId)
        public
        view
        override(ERC721, ERC721Enumerable)
        returns (bool)
    {
        return super.supportsInterface(interfaceId);
    }
}